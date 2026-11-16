import * as THREE from 'three';
import { OBJLoader } from 'three/addons/loaders/OBJLoader.js';
import { MTLLoader } from 'three/addons/loaders/MTLLoader.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const container = document.getElementById('canvas-container');

if (container) {
    const modelPath = container.dataset.model;
    const modelDir = modelPath.substring(0, modelPath.lastIndexOf('/') + 1);
    const ext = modelPath.split('.').pop().toLowerCase();

    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf0f0f0);

    const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 10000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    container.appendChild(renderer.domElement);

    scene.add(new THREE.AmbientLight(0xffffff, 1.8));
    const dirLight = new THREE.DirectionalLight(0xffffff, 2);
    dirLight.position.set(5, 10, 7);
    scene.add(dirLight);

    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.08;

    function setupModel(object) {
        object.rotation.set(0, -Math.PI / 2, 0); 
        object.updateMatrixWorld(true);

        // Лечим битую геометрию
        object.traverse((child) => {
            if (child.isMesh && child.geometry) {
                const position = child.geometry.attributes.position;
                if (position) {
                    let hasNaN = false;
                    for (let i = 0; i < position.array.length; i++) {
                        if (isNaN(position.array[i])) {
                            position.array[i] = 0;
                            hasNaN = true;
                        }
                    }
                    if (hasNaN) {
                        position.needsUpdate = true;
                        child.geometry.computeBoundingBox();
                        child.geometry.computeBoundingSphere();
                    }
                }
            }
        });

        const box = new THREE.Box3().setFromObject(object);
        
        if (box.isEmpty() || box.min.x === Infinity) {
            scene.add(object);
            camera.position.set(0, 1, 3);
            controls.target.set(0, 1, 0);
            controls.update();
            return;
        }

        const center = box.getCenter(new THREE.Vector3());
        const size = box.getSize(new THREE.Vector3()); 

        object.position.x -= center.x;
        object.position.z -= center.z;
        object.position.y -= box.min.y; 

        scene.add(object);

        const maxSize = Math.max(size.x, size.y, size.z);
        camera.position.set(0, size.y / 2, maxSize * 1.4); 
        controls.target.set(0, size.y / 2, 0);
        controls.minDistance = maxSize * 0.5; 
        controls.update();

        object.traverse((child) => {
            if (child.isMesh && child.material && child.material.map) {
                child.material.map.anisotropy = renderer.capabilities.getMaxAnisotropy();
                child.material.map.needsUpdate = true;
            }
        });
    }

    if (ext === 'obj') {
        const objLoader = new OBJLoader();
        const mtlLoader = new MTLLoader();
        mtlLoader.setResourcePath(modelDir);
        mtlLoader.setPath(modelDir);
        const mtlFile = modelPath.replace(/\.obj$/i, '.mtl').substring(modelDir.length);

        mtlLoader.load(mtlFile, (materials) => {
            materials.preload();
            objLoader.setMaterials(materials);
            objLoader.load(modelPath, (obj) => setupModel(obj));
        }, undefined, () => {
            objLoader.load(modelPath, (obj) => setupModel(obj));
        });
    } 
    else if (ext === 'glb' || ext === 'gltf') {
        const loader = new GLTFLoader();
        loader.load(modelPath, (gltf) => {
            gltf.scene.rotation.x = -Math.PI / 2; 
            setupModel(gltf.scene);
        });
    }

    (function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    })();

    window.addEventListener('resize', () => {
        const w = container.clientWidth;
        const h = container.clientHeight;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    });
}