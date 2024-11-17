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
            <h2 class="text-xl font-semibold mb-4">Customize Your Model</h2>
            <div class="flex items-center mb-4">
                <label for="modelSelector" class="mr-2">Choose Model:</label>
                <select id="modelSelector" class="border rounded">
                    <option value="models/tshirt.glb">T-Shirt</option>
                    <option value="models/hoodie.glb">Hoodie</option>
                    <option value="models/jersey.glb">Jersey</option>
                </select>
            </div>
            <div id="model-container">
                <div id="loader">Loading model...</div>
            </div>

            <div class="flex justify-around items-center mt-4">
                <div class="flex items-center">
                    <label for="colorPicker" class="mr-2">Choose Color:</label>
                    <input type="color" id="colorPicker" class="border rounded" />
                </div>
                <div class="flex items-center mb-4">
                    <label for="imageUpload" class="mr-2">Upload Image:</label>
                    <input type="file" id="imageUpload" accept="image/*" class="border rounded" />
                </div>
                <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" id="resetBtn">Reset</button>
                <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" id="saveBtn">Save</button>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-10">
        <p>&copy; 2024 Your Company. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/exporters/GLTFExporter.js"></script>

    <script>
        const container = document.getElementById('model-container');
        const loaderElement = document.getElementById('loader');
        const modelSelector = document.getElementById('modelSelector');
        let model, texturePlane;
        let isDragging = false;
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({
            antialias: true
        });

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

                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.material = new THREE.MeshStandardMaterial({
                                color: 0xffffff,
                                metalness: 0.5,
                                roughness: 0.5
                            });
                        }
                    });

                    camera.position.z = Math.max(box.max.x, box.max.y, box.max.z) * 1.3;
                    loaderElement.style.display = 'none';
                },
                undefined,
                (error) => {
                    console.error('Error loading model:', error);
                    loaderElement.textContent = 'Failed to load model.';
                }
            );
        };

        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.25;
        controls.screenSpacePanning = false;
        controls.maxPolarAngle = Math.PI / 2;

        const colorPicker = document.getElementById('colorPicker');
        colorPicker.addEventListener('input', (event) => {
            const selectedColor = event.target.value;
            if (model) {
                model.traverse((child) => {
                    if (child.isMesh) {
                        child.material.color.set(selectedColor);
                    }
                });
            }
        });

        const imageUpload = document.getElementById('imageUpload');
        imageUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const textureLoader = new THREE.TextureLoader();
                    textureLoader.load(e.target.result, (texture) => {
                        if (texturePlane) {
                            scene.remove(texturePlane);
                        }

                        const planeGeometry = new THREE.PlaneGeometry(1, 1);
                        const planeMaterial = new THREE.MeshBasicMaterial({
                            map: texture,
                            transparent: true
                        });
                        texturePlane = new THREE.Mesh(planeGeometry, planeMaterial);
                        // Position the plane slightly in front of the model (adjust as needed)
                        texturePlane.position.set(0, 0, 7); // Move forward along the Z-axis
                        texturePlane.scale.set(2, 2, 2); // Set a larger initial scale


                        scene.add(texturePlane);

                         // Make the plane draggable and scalable
                        setupDoubleClickTransform(texturePlane);

                        texturePlane.userData = {
                            isInteractive: true
                        };
                    });
                };
                reader.readAsDataURL(file);
            }
        });

        container.addEventListener('dblclick', (event) => {
            if (texturePlane) {
                isDragging = !isDragging;
                texturePlane.userData.isInteractive = isDragging;
                // console.log(isDragging ? "Drag mode enabled" : "Drag mode disabled"); // Debug log
            } else {
                // console.log("texturePlane not initialized"); // Debug log
            }
        });

        container.addEventListener('mousemove', (event) => {
            if (isDragging && texturePlane) {
                const rect = renderer.domElement.getBoundingClientRect();
                const x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                const y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
                texturePlane.position.set(x, y, texturePlane.position.z);
                // console.log("Moving texturePlane to:", x, y); // Debug log
            }
        });

        container.addEventListener('wheel', (event) => {
            if (isDragging && texturePlane) {
                const scale = Math.sign(event.deltaY) > 0 ? 0.9 : 1.1;
                texturePlane.scale.multiplyScalar(scale);
                // console.log("Scaling texturePlane by:", scale); // Debug log
            }
        });

        document.getElementById('resetBtn').addEventListener('click', () => {
            colorPicker.value = '#ffffff';
            if (model) {
                model.traverse((child) => {
                    if (child.isMesh) {
                        child.material.color.set(0xffffff);
                    }
                });
            }
        });

        document.getElementById('saveBtn').addEventListener('click', () => {
            const exporter = new THREE.GLTFExporter();
            exporter.parse(scene, (result) => {
                const blob = new Blob([result], {
                    type: 'application/octet-stream'
                });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'customized_model.glb';
                link.click();
            });
        });

        const animate = () => {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        };

        addLights();
        loadModel(modelSelector.value);
        animate();

        modelSelector.addEventListener('change', (event) => {
            loaderElement.style.display = 'block';
            loadModel(event.target.value);
        });

        window.addEventListener('resize', () => {
            renderer.setSize(container.clientWidth, container.clientHeight);
            camera.aspect = container.clientWidth / container.clientHeight;
            camera.updateProjectionMatrix();
        });
    </script>
</body>

</html>