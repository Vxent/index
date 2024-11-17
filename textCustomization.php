<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Customization</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        #model-container {
            width: 100%;
            height: 500px;
            border: 2px solid gray;
            background-color: lightgray;
            overflow: hidden;
            position: relative;
        }

        #loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5rem;
            color: gray;
        }
    </style>
</head>

<body class="bg-gray-100">
    <header class="bg-blue-600 text-white p-4">
        <h1 class="text-2xl font-bold">3D Customization Tool</h1>
    </header>

    <main class="flex flex-col items-center justify-center py-10">
        <div class="w-full max-w-4xl bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">View Your Model</h2>
            <div class="flex items-center mb-4">
                <label for="modelSelector" class="mr-2">Choose Model:</label>
                <select id="modelSelector" class="border rounded">
                    <option value="models/tshirt.glb">T-Shirt</option>
                    <option value="models/hoodie.glb">Hoodie</option>
                    <option value="models/jersey.glb">Jersey</option>
                    <!-- Add more models as needed -->
                </select>
            </div>
            <div id="model-container">
                <div id="loader">Loading model...</div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-10">
        <p>&copy; 2024 Your Company. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/geometries/TextGeometry.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/FontLoader.js"></script>
    <script>
        const container = document.getElementById('model-container');
        const loaderElement = document.getElementById('loader');
        const modelSelector = document.getElementById('modelSelector');
        let model;
        let textMesh;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });

        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

        const addLights = () => {
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(ambientLight);
            const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
            directionalLight.position.set(5, 5, 5);
            scene.add(directionalLight);
        };

        const loadModel = (modelPath) => {
            if (model) {
                scene.remove(model);
            }
            const loader = new THREE.GLTFLoader();
            loader.load(
                modelPath,
                (gltf) => {
                    model = gltf.scene;
                    model.scale.set(0.5, 0.5, 0.5);
                    scene.add(model);

                    const box = new THREE.Box3().setFromObject(model);
                    const center = box.getCenter(new THREE.Vector3());
                    model.position.set(-center.x, -center.y, 0);
                    model.position.y += model.scale.y * (box.max.y - box.min.y) / 5;

                    camera.position.z = Math.max(box.max.x, box.max.y, box.max.z) * 1.3;
                    loaderElement.style.display = 'none';

                    addText('Editable Text'); // Add initial text
                },
                undefined,
                (error) => {
                    console.error('Error loading model:', error);
                    loaderElement.textContent = 'Failed to load model.';
                }
            );
        };

        const addText = (text) => {
            const fontLoader = new THREE.FontLoader();
            fontLoader.load('https://threejs.org/examples/fonts/helvetiker_regular.typeface.json', (font) => {
                const geometry = new THREE.TextGeometry(text, {
                    font: font,
                    size: 0.5,
                    height: 0.1,
                });
                const material = new THREE.MeshBasicMaterial({ color: 0x00ff00 });
                textMesh = new THREE.Mesh(geometry, material);
                textMesh.position.set(0, 1, 0); // Adjust position above the model
                scene.add(textMesh);

                // Add double-click event for text editing
                textMesh.userData.editable = true; // Mark as editable
            });
        };

        const updateText = (newText) => {
            scene.remove(textMesh);
            addText(newText);
        };

        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.25;
        controls.screenSpacePanning = false;
        controls.maxPolarAngle = Math.PI / 2;

        window.addEventListener('resize', () => {
            const width = container.clientWidth;
            const height = container.clientHeight;
            renderer.setSize(width, height);
            camera.aspect = width / height;
            camera.updateProjectionMatrix();
        });

        modelSelector.addEventListener('change', (event) => {
            const modelPath = event.target.value;
            loaderElement.style.display = 'block'; // Show loader
            loadModel(modelPath);
        });

        // Handle double-click event
        window.addEventListener('dblclick', (event) => {
            event.preventDefault();
            const mouse = new THREE.Vector2();
            const raycaster = new THREE.Raycaster();

            mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
            mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;

            raycaster.setFromCamera(mouse, camera);
            const intersects = raycaster.intersectObjects(scene.children);
            if (intersects.length > 0) {
                const object = intersects[0].object;
                if (object.userData.editable) {
                    const newText = prompt('Enter new text:', textMesh.geometry.parameters.text);
                    if (newText !== null) {
                        updateText(newText);
                    }
                }
            }
        });

        const animate = () => {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        };

        addLights();
        loadModel(modelSelector.value); // Load initial model
        animate();
    </script>
</body>

</html>
