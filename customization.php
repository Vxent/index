
<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>3D Customization Tool</title>
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
<body style="background-image: url(http://www.pixelstalk.net/wp-content/uploads/2016/10/Black-and-Orange-Background-Full-HD.jpg);">

    <!-- Header -->
    <header class="bg-yellow-800 text-white p-4 flex justify-between items-center p-3" >
    <h1 class="text-2xl font-bold ">3D Customization Tool</h1>
    <a href="index.php"><button class="py-2 px-6 border-2 border-white bg-gradient-to-r from-yellow-500 to-orange-600 text-white py-2 rounded-md shadow-lg hover:from-yellow-600  hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
        Back
    </button></a>
</header>


    <!-- Main Content -->
    <main class="flex justify-center items-center py-10">
        <div class="flex w-full max-w-6xl  rounded-lg shadow-md bg-white">

            <!-- Left Sidebar for Customization -->
            <div class="sidebar flex flex-col w-1/4  p-6  border-4 border-black">
                <h2 class="text-xl font-semibold mb-4">Customize Your Model</h2>

                <!-- Product Selector -->
                <div class="model-selector mb-4 ">
                    <label for="modelSelector" class="block text-sm font-medium">Choose Model:</label>
                    <select id="modelSelector" class="border rounded p-2 w-full">
                        <option value="models/tshirt.glb">T-Shirt</option>
                        <option value="models/hoodie.glb">Hoodie</option>
                        <option value="models/jersey.glb">Jersey</option>
                    </select>
                </div>

                <!-- Color Picker -->
                <div class="color-picker mb-4">
                    <label for="colorPicker" class="block text-sm font-medium">Choose Color:</label>
                    <input type="color" id="colorPicker" class="border rounded p-2 w-full" />

                    <!-- Color Code Input -->
                 <label for="colorCode" class="block text-sm font-medium mt-2">Or enter Color Code:</label>
                <input type="text" id="colorCode" class="border rounded p-2 w-full" placeholder="#ffffff" maxlength="7"/>
                </div>

                <!-- Text Input for Custom Text -->
                <div class="text-input mb-4">
                    <label for="customText" class="block text-sm font-medium">Enter Custom Text:</label>
                    <input type="text" id="customText" class="border rounded p-2 w-full" placeholder="Enter text here" />
                    <button id="applyTextBtn" class="bg-blue-500 text-white px-4 py-2 rounded mt-2 hover:bg-blue-600">Apply Text</button>
                </div>

                <!-- Image Upload -->
                <div class="image-upload mb-4">
                    <label for="imageUpload" class="block text-sm font-medium">Upload Design Image:</label>
                    <input type="file" id="imageUpload" class="border rounded p-2 w-full" accept="image/*" />
                </div>

                <!-- Buttons -->
                <div class="buttons mt-4 flex justify-between">
                    <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" id="resetBtn">Reset</button>
                    <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" id="saveBtn">Save</button>
                </div>

                <!-- Submit Order Button -->
                <button id="submitBtn" class="bg-yellow-500 text-white px-4 py-2 rounded mt-4 hover:bg-yellow-600">
                    Submit Order
                </button>
            </div>

            <!-- Model Viewer Container -->
            <div class="flex-1 p-6 bg-white rounded-r-lg  border-4 border-black">
                <div id="model-container">
                    <div id="loader">Loading model...</div>
                </div>
            </div>
        </div>
    </main>



    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/build/three.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/exporters/GLTFExporter.js"></script>
    <script>
        const container = document.getElementById('model-container');
        const loaderElement = document.getElementById('loader');
        const modelSelector = document.getElementById('modelSelector');
        const colorPicker = document.getElementById('colorPicker');
        const imageUpload = document.getElementById('imageUpload');
        const submitBtn = document.getElementById('submitBtn');
        const resetBtn = document.getElementById('resetBtn');
        const saveBtn = document.getElementById('saveBtn');
        let model;

        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(75, container.clientWidth / container.clientHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ antialias: true });

        renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(renderer.domElement);

        // Add lights
        const addLights = () => {
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
            scene.add(ambientLight);
            const directionalLight = new THREE.DirectionalLight(0xffffff, 1);
            directionalLight.position.set(5, 5, 5);
            scene.add(directionalLight);
        };

        // Load 3D Model
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

        // Apply image as texture
        const applyImageTexture = (image) => {
            const textureLoader = new THREE.TextureLoader();
            textureLoader.load(image, (texture) => {
                if (model) {
                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.material.map = texture;
                            child.material.needsUpdate = true;
                        }
                    });
                }
            });
        };

        // Set up camera controls
        const controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.25;
        controls.screenSpacePanning = false;
        controls.maxPolarAngle = Math.PI / 2;

        // Handle model color changes
        colorPicker.addEventListener('input', (event) => {
            const selectedColor = event.target.value;
            if (model) {
                model.traverse((child) => {
                    if (child.isMesh) {
                        child.material.color.set(selectedColor);
                    }
                });
            }

            // Update the color code input to match the selected color
            colorCode.value = selectedColor;
        });

        // Handle color changes from the color code input
        colorCode.addEventListener('input', (event) => {
            const hexCode = event.target.value;

            // Check if the input is a valid hex code (basic validation)
            if (/^#[0-9A-Fa-f]{6}$/.test(hexCode)) {
                if (model) {
                    model.traverse((child) => {
                        if (child.isMesh) {
                            child.material.color.set(hexCode);
                        }
                    });
                }
            } else {
                // Optionally, you can show an error message if the HEX code is invalid
                console.warn("Invalid color code");
            }
        });

        // Add a global variable for the 3D text
        let textMesh;

        // Function to create and display 3D text
        const create3DText = (text) => {
            // Remove existing text if any
            if (textMesh) {
                scene.remove(textMesh);
            }

            // Create font loader
            const fontLoader = new THREE.FontLoader();
            fontLoader.load('https://threejs.org/examples/fonts/helvetiker_regular.typeface.json', (font) => {
                const geometry = new THREE.TextGeometry(text, {
                    font: font,
                    size: 1,
                    height: 0.2,
                });

                const material = new THREE.MeshBasicMaterial({ color: 0x000000 }); // Black color for the text
                textMesh = new THREE.Mesh(geometry, material);

                // Center the text in front of the model
                const box = new THREE.Box3().setFromObject(model);
                const center = box.getCenter(new THREE.Vector3());
                textMesh.position.set(center.x, center.y, box.max.z + 1); // Position it slightly in front of the model

                scene.add(textMesh);
            });
        };

        // Event listener for the "Apply Text" button
        document.getElementById('applyTextBtn').addEventListener('click', () => {
            const customText = document.getElementById('customText').value;
            if (customText) {
                create3DText(customText); // Apply the custom text to the model
            } else {
                alert("Please enter some text.");
            }
        });


       // Reset model modifications
        resetBtn.addEventListener('click', () => {
            // Reset color picker to white
            colorPicker.value = '#ffffff';
            colorCode.value = '#ffffff';
            
            // Reset color on the model to white
            if (model) {
            model.traverse((child) => {
            if (child.isMesh) {
                // Reset color to white
                child.material.color.set(0xffffff);
            }
        });
    }

            // Optionally, you may want to reload the default model if any modifications
            // have been made that can't be reset (like textures).
            loadModel(modelSelector.value);
        });


        // Save customized model
        saveBtn.addEventListener('click', () => {
            const exporter = new THREE.GLTFExporter();
            exporter.parse(scene, (result) => {
                const blob = new Blob([result], { type: 'application/octet-stream' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = 'custom_model.glb';
                link.click();
            }, { binary: true });
        });

        // Handle submit button click - redirect to the form page
        submitBtn.addEventListener('click', () => {
            const modelPath = modelSelector.value;
            const selectedColor = colorPicker.value;
            const productName = modelSelector.options[modelSelector.selectedIndex].text;

            localStorage.setItem('selectedModel', modelPath);
            localStorage.setItem('selectedColor', selectedColor);
            localStorage.setItem('selectedProductName', productName);

            window.location.href = 'customizationFormPage.php';
        });

        // Load selected model
        modelSelector.addEventListener('change', (event) => {
            const modelPath = event.target.value;
            loaderElement.style.display = 'block'; // Show loader
            loadModel(modelPath);
        });

        // Handle image upload
        imageUpload.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    applyImageTexture(e.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // Animation loop
        const animate = () => {
            requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        };

        // Initialize scene setup
        addLights();
        loadModel(modelSelector.value); // Load initial model
        animate();
    </script>
</body>
</html>
