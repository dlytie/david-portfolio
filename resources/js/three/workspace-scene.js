import * as THREE from 'three';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { supportsWebGL } from '../utils/webgl-support';

gsap.registerPlugin(ScrollTrigger);

const palette = {
    dark: 0x111827,
    darker: 0x070a11,
    slate: 0x263244,
    cyan: 0x22d3ee,
    violet: 0x8b5cf6,
    green: 0x34d399,
    white: 0xf8fafc,
};

export async function initWorkspaceScene() {
    const root = document.querySelector('[data-scene-root]');
    const canvas = root?.querySelector('[data-scene-canvas]');

    if (!root || !canvas) {
        return () => {};
    }

    if (!supportsWebGL()) {
        root.classList.add('is-fallback');
        return () => {};
    }

    const isMobile = window.matchMedia('(max-width: 820px)').matches;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const lowPower = isMobile || (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 4);
    const assetBase = root.dataset.assetBase;
    const customModelPath = root.dataset.customModel;

    const renderer = new THREE.WebGLRenderer({
        canvas,
        alpha: true,
        antialias: !lowPower,
        powerPreference: 'high-performance',
    });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1.15 : 1.5));
    renderer.setSize(window.innerWidth, window.innerHeight, false);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.04;
    renderer.shadowMap.enabled = !lowPower;
    renderer.shadowMap.type = THREE.PCFShadowMap;

    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(palette.darker, 0.042);

    const camera = new THREE.PerspectiveCamera(34, window.innerWidth / window.innerHeight, 0.1, 100);
    const cameraStart = isMobile
        ? new THREE.Vector3(7.2, 4.8, 10.8)
        : new THREE.Vector3(7.4, 4.9, 9.2);
    const lookTarget = new THREE.Vector3(isMobile ? 1.6 : 2.1, 1.45, 0);
    camera.position.copy(cameraStart);

    const sceneRig = new THREE.Group();
    const workspace = new THREE.Group();
    const defaultWorkspace = new THREE.Group();
    workspace.position.set(isMobile ? 1.1 : 2.65, -1.05, -0.25);
    workspace.add(defaultWorkspace);
    sceneRig.add(workspace);
    scene.add(sceneRig);

    addLighting(scene, lowPower);
    addBackdrop(scene, lowPower);

    const interactiveRoots = [];
    const animated = createProceduralWorkspace(defaultWorkspace, interactiveRoots, lowPower);

    const assetSpecs = [
        { file: 'chairDesk.glb', size: 2.25, position: [0, 0, -1.58], rotationY: Math.PI, tint: 0x1b2535 },
        { file: 'lampSquareTable.glb', size: 1.45, position: [1.75, 1.58, -0.18], rotationY: -0.35, tint: 0x263244 },
        { file: 'laptop.glb', size: 1.28, position: [-1.5, 1.58, 0.12], rotationY: 0.12, tint: 0x334155 },
        { file: 'plantSmall2.glb', size: 1.25, position: [2.38, 0.06, 1.35], rotationY: 0.4, tint: 0x2f765c },
        { file: 'speakerSmall.glb', size: 0.72, position: [-1.2, 1.59, -0.2], rotationY: 0, tint: 0x1f2937 },
        { file: 'speakerSmall.glb', size: 0.72, position: [1.2, 1.59, -0.2], rotationY: 0, tint: 0x1f2937 },
        { file: 'books.glb', size: 0.82, position: [2.05, 1.59, 0.64], rotationY: -0.15, tint: 0x8b5cf6 },
    ];

    const assetsToLoad = lowPower ? assetSpecs.slice(0, 4) : assetSpecs;
    const loader = new GLTFLoader();
    const assetResults = await Promise.allSettled(
        assetsToLoad.map((spec) => loadAsset(loader, `${assetBase}/${spec.file}`, spec, defaultWorkspace)),
    );

    let loadedCount = assetResults.filter((result) => result.status === 'fulfilled').length;
    const failedAssets = assetResults.filter((result) => result.status === 'rejected');

    root.dataset.failedAssets = String(failedAssets.length);
    if (failedAssets[0]?.reason instanceof Error) {
        root.dataset.assetError = failedAssets[0].reason.message;
    }

    if (customModelPath) {
        try {
            const gltf = await loader.loadAsync(customModelPath);
            const customWorkspace = gltf.scene;
            normalizeAndGround(customWorkspace, 7.4);
            customWorkspace.traverse((child) => {
                if (child.isMesh) {
                    child.castShadow = !lowPower;
                    child.receiveShadow = true;
                }
            });
            workspace.add(customWorkspace);
            defaultWorkspace.visible = false;
            loadedCount += 1;
        } catch {
            defaultWorkspace.visible = true;
        }
    }
    root.dataset.loadedAssets = String(loadedCount);
    root.classList.add('is-ready');

    const pointer = new THREE.Vector2(0, 0);
    const pointerTarget = new THREE.Vector2(0, 0);
    const raycaster = new THREE.Raycaster();
    const tooltip = document.querySelector('[data-scene-tooltip]');
    let hoveredAction = null;
    let running = true;
    let disposed = false;
    const timer = new THREE.Timer();
    timer.connect(document);

    const scrollTimeline = createScrollTimeline({
        camera,
        lookTarget,
        sceneRig,
        reducedMotion,
        isMobile,
    });

    const onPointerMove = (event) => {
        pointerTarget.x = (event.clientX / window.innerWidth) * 2 - 1;
        pointerTarget.y = -(event.clientY / window.innerHeight) * 2 + 1;

        if (tooltip) {
            tooltip.style.left = `${event.clientX}px`;
            tooltip.style.top = `${event.clientY}px`;
        }
    };

    const onClick = (event) => {
        if (
            !hoveredAction
            || event.target.closest('a, button, input, textarea, dialog, nav, form')
        ) {
            return;
        }

        const target = document.querySelector(hoveredAction === 'projects' ? '#projects' : '#skills');
        target?.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth' });
    };

    const onResize = () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, lowPower ? 1.15 : 1.5));
        renderer.setSize(window.innerWidth, window.innerHeight, false);
    };

    const onVisibilityChange = () => {
        running = !document.hidden;
    };

    window.addEventListener('pointermove', onPointerMove, { passive: true });
    window.addEventListener('click', onClick);
    window.addEventListener('resize', onResize, { passive: true });
    document.addEventListener('visibilitychange', onVisibilityChange);

    const animate = (timestamp) => {
        if (disposed) {
            return;
        }

        requestAnimationFrame(animate);

        if (!running) {
            return;
        }

        timer.update(timestamp);
        const elapsed = timer.getElapsed();
        pointer.lerp(pointerTarget, reducedMotion ? 0.02 : 0.045);

        if (!reducedMotion) {
            workspace.rotation.y += ((pointer.x * 0.08) - workspace.rotation.y) * 0.035;
            workspace.rotation.x += ((pointer.y * -0.025) - workspace.rotation.x) * 0.035;
            animated.networkRings.forEach((ring, index) => {
                const phase = (elapsed * 0.72 + index * 0.45) % 1.8;
                ring.scale.setScalar(0.75 + phase * 0.42);
                ring.material.opacity = Math.max(0, 0.32 - phase * 0.17);
            });
            animated.statusLight.material.emissiveIntensity = 1.35 + Math.sin(elapsed * 2.2) * 0.4;
            animated.particles.rotation.y = elapsed * 0.018;
        }

        raycaster.setFromCamera(pointerTarget, camera);
        const intersection = raycaster.intersectObjects(interactiveRoots, true)[0];
        const actionTarget = findActionTarget(intersection?.object);
        hoveredAction = actionTarget?.userData.action ?? null;

        if (tooltip) {
            tooltip.classList.toggle('is-visible', Boolean(hoveredAction));
            tooltip.textContent = hoveredAction === 'projects'
                ? 'Buka proyek pilihan'
                : hoveredAction === 'skills'
                    ? 'Lihat keahlian jaringan'
                    : '';
        }

        camera.lookAt(lookTarget);
        renderer.render(scene, camera);
    };

    animate();

    return () => {
        disposed = true;
        running = false;
        scrollTimeline?.scrollTrigger?.kill();
        scrollTimeline?.kill();
        window.removeEventListener('pointermove', onPointerMove);
        window.removeEventListener('click', onClick);
        window.removeEventListener('resize', onResize);
        document.removeEventListener('visibilitychange', onVisibilityChange);
        timer.dispose();
        disposeScene(scene);
        renderer.dispose();
    };
}

function addLighting(scene, lowPower) {
    const hemisphere = new THREE.HemisphereLight(0xcaf7ff, 0x0a0d16, 1.75);
    scene.add(hemisphere);

    const key = new THREE.DirectionalLight(0xe9fbff, 2.4);
    key.position.set(6, 9, 7);
    key.castShadow = !lowPower;
    key.shadow.mapSize.set(1024, 1024);
    key.shadow.camera.near = 0.5;
    key.shadow.camera.far = 30;
    scene.add(key);

    const rim = new THREE.PointLight(palette.violet, 14, 17, 2);
    rim.position.set(-3, 4, -3);
    scene.add(rim);

    const monitorGlow = new THREE.PointLight(palette.cyan, 12, 10, 2);
    monitorGlow.position.set(2.4, 2.7, 3);
    scene.add(monitorGlow);
}

function addBackdrop(scene, lowPower) {
    const floorMaterial = new THREE.MeshStandardMaterial({
        color: 0x0b111c,
        roughness: 0.86,
        metalness: 0.08,
        transparent: true,
        opacity: 0.92,
    });
    const floor = new THREE.Mesh(new THREE.CircleGeometry(8.5, lowPower ? 32 : 64), floorMaterial);
    floor.rotation.x = -Math.PI / 2;
    floor.position.set(2.2, -1.04, -0.15);
    floor.receiveShadow = !lowPower;
    scene.add(floor);

    const grid = new THREE.GridHelper(18, lowPower ? 20 : 36, palette.cyan, 0x1c2738);
    grid.position.set(2.2, -1.02, -0.15);
    grid.material.transparent = true;
    grid.material.opacity = 0.11;
    scene.add(grid);
}

function createProceduralWorkspace(workspace, interactiveRoots, lowPower) {
    const darkMaterial = new THREE.MeshStandardMaterial({ color: palette.dark, roughness: 0.55, metalness: 0.4 });
    const slateMaterial = new THREE.MeshStandardMaterial({ color: palette.slate, roughness: 0.72, metalness: 0.18 });
    const blackMaterial = new THREE.MeshStandardMaterial({ color: 0x080c14, roughness: 0.48, metalness: 0.34 });
    const cyanMaterial = new THREE.MeshStandardMaterial({
        color: palette.cyan,
        emissive: palette.cyan,
        emissiveIntensity: 1.3,
        roughness: 0.28,
        metalness: 0.2,
    });

    const platform = new THREE.Mesh(
        new THREE.CylinderGeometry(3.65, 3.9, 0.22, lowPower ? 32 : 64),
        new THREE.MeshStandardMaterial({ color: 0x0e1521, roughness: 0.7, metalness: 0.2 }),
    );
    platform.position.y = 0;
    platform.receiveShadow = !lowPower;
    workspace.add(platform);

    const platformRing = new THREE.Mesh(
        new THREE.TorusGeometry(3.73, 0.024, 8, lowPower ? 48 : 96),
        new THREE.MeshBasicMaterial({ color: palette.cyan, transparent: true, opacity: 0.34 }),
    );
    platformRing.rotation.x = Math.PI / 2;
    platformRing.position.y = 0.12;
    workspace.add(platformRing);

    const desk = new THREE.Group();
    const deskTop = box(4.9, 0.17, 2.15, slateMaterial);
    deskTop.position.y = 1.52;
    deskTop.castShadow = !lowPower;
    desk.add(deskTop);

    [-1.9, 1.9].forEach((x) => {
        const leg = box(0.13, 1.42, 1.72, darkMaterial);
        leg.position.set(x, 0.76, 0);
        leg.castShadow = !lowPower;
        desk.add(leg);
    });
    workspace.add(desk);

    const monitor = new THREE.Group();
    monitor.userData.action = 'projects';
    monitor.userData.label = 'Buka proyek pilihan';
    const monitorBody = box(2.28, 1.34, 0.15, blackMaterial);
    monitorBody.position.y = 2.5;
    monitor.add(monitorBody);

    const screen = new THREE.Mesh(
        new THREE.PlaneGeometry(2.08, 1.14),
        new THREE.MeshBasicMaterial({ map: createMonitorTexture(), toneMapped: false }),
    );
    screen.position.set(0, 2.5, 0.081);
    monitor.add(screen);

    const stand = box(0.12, 0.56, 0.12, darkMaterial);
    stand.position.set(0, 1.72, 0);
    monitor.add(stand);
    const standBase = box(0.78, 0.08, 0.38, darkMaterial);
    standBase.position.set(0, 1.48, 0.1);
    monitor.add(standBase);
    monitor.traverse((child) => {
        child.userData.action = 'projects';
    });
    workspace.add(monitor);
    interactiveRoots.push(monitor);

    const keyboard = box(1.46, 0.08, 0.48, blackMaterial);
    keyboard.position.set(0, 1.65, 0.72);
    keyboard.rotation.x = -0.035;
    workspace.add(keyboard);
    addKeyboardKeys(workspace, lowPower);

    const mouse = new THREE.Mesh(new THREE.SphereGeometry(0.15, 18, 10), blackMaterial);
    mouse.scale.set(0.78, 0.4, 1.15);
    mouse.position.set(1.02, 1.67, 0.73);
    workspace.add(mouse);

    const server = new THREE.Group();
    const serverBody = box(0.56, 0.72, 0.92, blackMaterial);
    serverBody.position.set(-2.05, 0.58, 0.2);
    server.add(serverBody);
    const statusLight = new THREE.Mesh(new THREE.SphereGeometry(0.035, 12, 12), cyanMaterial);
    statusLight.position.set(-2.05, 0.74, 0.67);
    server.add(statusLight);
    workspace.add(server);

    const router = createRouter(darkMaterial, cyanMaterial, lowPower);
    router.position.set(1.42, 1.67, 0.68);
    workspace.add(router);
    interactiveRoots.push(router);

    const networkRings = createNetworkRings(router);
    const particles = createParticles(lowPower);
    workspace.add(particles);

    return { networkRings, statusLight, particles };
}

function createRouter(bodyMaterial, accentMaterial, lowPower) {
    const router = new THREE.Group();
    router.userData.action = 'skills';

    const body = box(0.76, 0.13, 0.46, bodyMaterial);
    router.add(body);

    [-0.25, 0.25].forEach((x) => {
        const antenna = new THREE.Mesh(new THREE.CylinderGeometry(0.018, 0.018, 0.58, 8), bodyMaterial);
        antenna.position.set(x, 0.31, -0.16);
        antenna.rotation.z = x < 0 ? -0.16 : 0.16;
        router.add(antenna);
    });

    for (let index = 0; index < 3; index += 1) {
        const light = new THREE.Mesh(new THREE.SphereGeometry(0.018, lowPower ? 6 : 10, lowPower ? 6 : 10), accentMaterial);
        light.position.set(-0.09 + index * 0.09, 0.073, 0.235);
        router.add(light);
    }

    router.traverse((child) => {
        child.userData.action = 'skills';
    });

    return router;
}

function createNetworkRings(router) {
    const rings = [];

    for (let index = 0; index < 3; index += 1) {
        const material = new THREE.MeshBasicMaterial({
            color: palette.cyan,
            transparent: true,
            opacity: 0.2,
            side: THREE.DoubleSide,
        });
        const ring = new THREE.Mesh(new THREE.TorusGeometry(0.23 + index * 0.11, 0.009, 6, 48, Math.PI), material);
        ring.rotation.x = Math.PI / 2;
        ring.rotation.z = Math.PI / 2;
        ring.position.y = 0.44;
        router.add(ring);
        rings.push(ring);
    }

    return rings;
}

function createParticles(lowPower) {
    const count = lowPower ? 40 : 90;
    const positions = new Float32Array(count * 3);

    for (let index = 0; index < count; index += 1) {
        const radius = 3.8 + Math.random() * 3.2;
        const angle = Math.random() * Math.PI * 2;
        positions[index * 3] = Math.cos(angle) * radius;
        positions[index * 3 + 1] = 0.6 + Math.random() * 4.2;
        positions[index * 3 + 2] = Math.sin(angle) * radius;
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    const material = new THREE.PointsMaterial({
        color: palette.cyan,
        size: lowPower ? 0.018 : 0.025,
        transparent: true,
        opacity: 0.3,
        sizeAttenuation: true,
    });

    return new THREE.Points(geometry, material);
}

function addKeyboardKeys(workspace, lowPower) {
    const rows = lowPower ? 3 : 4;
    const columns = lowPower ? 9 : 12;
    const geometry = new THREE.BoxGeometry(0.085, 0.025, 0.07);
    const material = new THREE.MeshStandardMaterial({ color: 0x293446, roughness: 0.6 });
    const keys = new THREE.InstancedMesh(geometry, material, rows * columns);
    const dummy = new THREE.Object3D();
    let index = 0;

    for (let row = 0; row < rows; row += 1) {
        for (let column = 0; column < columns; column += 1) {
            dummy.position.set(-0.49 + column * (0.98 / (columns - 1)), 1.705, 0.59 + row * 0.08);
            dummy.updateMatrix();
            keys.setMatrixAt(index, dummy.matrix);
            index += 1;
        }
    }

    workspace.add(keys);
}

async function loadAsset(loader, url, spec, parent) {
    const gltf = await loader.loadAsync(url);
    const object = gltf.scene;
    normalizeAndGround(object, spec.size);
    tintObject(object, spec.tint);
    object.position.set(...spec.position);
    object.rotation.y = spec.rotationY ?? 0;
    object.name = spec.file;
    parent.add(object);

    return object;
}

function normalizeAndGround(object, targetSize) {
    object.updateMatrixWorld(true);
    const originalBox = new THREE.Box3().setFromObject(object);
    const originalSize = originalBox.getSize(new THREE.Vector3());
    const largest = Math.max(originalSize.x, originalSize.y, originalSize.z) || 1;
    object.scale.multiplyScalar(targetSize / largest);
    object.updateMatrixWorld(true);

    const box = new THREE.Box3().setFromObject(object);
    const center = box.getCenter(new THREE.Vector3());
    object.position.x -= center.x;
    object.position.z -= center.z;
    object.position.y -= box.min.y;
}

function tintObject(object, tint) {
    const tintColor = new THREE.Color(tint);

    object.traverse((child) => {
        if (!child.isMesh) {
            return;
        }

        const sourceMaterials = Array.isArray(child.material) ? child.material : [child.material];
        const materials = sourceMaterials.map((source) => {
            const originalColor = source?.color?.clone() ?? new THREE.Color(0xffffff);
            const color = originalColor.lerp(tintColor, 0.24);

            return new THREE.MeshStandardMaterial({
                color,
                map: source?.map ?? null,
                roughness: 0.7,
                metalness: 0.08,
            });
        });

        child.material = Array.isArray(child.material) ? materials : materials[0];
        child.castShadow = true;
        child.receiveShadow = true;
    });
}

function createMonitorTexture() {
    const canvas = document.createElement('canvas');
    canvas.width = 1024;
    canvas.height = 560;
    const context = canvas.getContext('2d');

    const gradient = context.createLinearGradient(0, 0, canvas.width, canvas.height);
    gradient.addColorStop(0, '#07101b');
    gradient.addColorStop(0.56, '#0b1724');
    gradient.addColorStop(1, '#11102a');
    context.fillStyle = gradient;
    context.fillRect(0, 0, canvas.width, canvas.height);

    context.strokeStyle = 'rgba(34, 211, 238, 0.12)';
    context.lineWidth = 1;
    for (let x = 0; x < canvas.width; x += 64) {
        context.beginPath();
        context.moveTo(x, 0);
        context.lineTo(x, canvas.height);
        context.stroke();
    }
    for (let y = 0; y < canvas.height; y += 64) {
        context.beginPath();
        context.moveTo(0, y);
        context.lineTo(canvas.width, y);
        context.stroke();
    }

    context.fillStyle = '#22d3ee';
    context.font = '600 23px monospace';
    context.fillText('DAVID.OS / PORTFOLIO', 58, 70);

    context.fillStyle = '#f8fafc';
    context.font = '600 64px sans-serif';
    context.fillText('Systems online.', 58, 188);

    context.fillStyle = '#94a3b8';
    context.font = '400 25px sans-serif';
    context.fillText('IT support · infrastructure · Laravel', 58, 237);

    ['NETWORK', 'SERVER', 'WEB APP'].forEach((label, index) => {
        const x = 58 + index * 280;
        context.fillStyle = index === 1 ? '#8b5cf6' : '#22d3ee';
        context.fillRect(x, 320, 14, 14);
        context.fillStyle = '#cbd5e1';
        context.font = '600 17px monospace';
        context.fillText(label, x + 28, 333);
        context.fillStyle = 'rgba(148,163,184,.3)';
        context.fillRect(x, 363, 212, 6);
        context.fillStyle = index === 1 ? '#8b5cf6' : '#22d3ee';
        context.fillRect(x, 363, 100 + index * 42, 6);
    });

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.anisotropy = 4;
    return texture;
}

function createScrollTimeline({ camera, lookTarget, sceneRig, reducedMotion, isMobile }) {
    if (reducedMotion || isMobile) {
        return null;
    }

    return gsap.timeline({
        scrollTrigger: {
            trigger: document.body,
            start: 'top top',
            end: 'bottom bottom',
            scrub: 1.15,
        },
    })
        .to(camera.position, { x: 6.4, y: 4.15, z: 8.15, duration: 1 }, 0.12)
        .to(lookTarget, { x: 2.0, y: 1.65, z: -0.2, duration: 1 }, 0.12)
        .to(sceneRig.rotation, { y: -0.16, duration: 1 }, 0.12)
        .to(camera.position, { x: 4.5, y: 3.45, z: 7.1, duration: 1 }, 1.12)
        .to(lookTarget, { x: 2.45, y: 1.5, z: 0.05, duration: 1 }, 1.12)
        .to(sceneRig.rotation, { y: 0.13, duration: 1 }, 1.12)
        .to(camera.position, { x: 7.6, y: 4.15, z: 6.55, duration: 1 }, 2.12)
        .to(lookTarget, { x: 2.2, y: 1.55, z: 0, duration: 1 }, 2.12)
        .to(sceneRig.rotation, { y: 0.26, duration: 1 }, 2.12)
        .to(sceneRig.position, { y: -0.28, duration: 0.8 }, 3.08);
}

function findActionTarget(object) {
    let current = object;

    while (current) {
        if (current.userData?.action) {
            return current;
        }
        current = current.parent;
    }

    return null;
}

function box(width, height, depth, material) {
    return new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), material);
}

function disposeScene(scene) {
    scene.traverse((object) => {
        object.geometry?.dispose?.();

        const materials = Array.isArray(object.material) ? object.material : [object.material];
        materials.filter(Boolean).forEach((material) => {
            Object.values(material).forEach((value) => {
                if (value?.isTexture) {
                    value.dispose();
                }
            });
            material.dispose?.();
        });
    });
}
