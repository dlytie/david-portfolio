import Alpine from 'alpinejs';
import { initPageMotion } from './motion';
import { initNavigation } from './navigation';

window.Alpine = Alpine;

Alpine.data('portfolioApp', () => ({
    menuOpen: false,
    activeProject: null,
    lastProjectTrigger: null,
    contactSubmitting: false,

    openProject(slug, trigger) {
        this.activeProject = slug;
        this.lastProjectTrigger = trigger;

        this.$nextTick(() => {
            if (!this.$refs.projectDialog.open) {
                this.$refs.projectDialog.showModal();
            }

            document.body.classList.add('dialog-open');
        });
    },

    closeProject() {
        if (this.$refs.projectDialog.open) {
            this.$refs.projectDialog.close();
        }
    },

    closeDialogFromBackdrop(event) {
        if (event.target === this.$refs.projectDialog) {
            this.closeProject();
        }
    },

    onDialogClosed() {
        document.body.classList.remove('dialog-open');
        this.activeProject = null;

        if (this.lastProjectTrigger) {
            this.lastProjectTrigger.focus({ preventScroll: true });
        }
    },
}));

Alpine.start();

const cleanups = [initNavigation(), initPageMotion()];

if (document.querySelector('[data-scene-root]')) {
    import('./three/workspace-scene')
        .then(({ initWorkspaceScene }) => initWorkspaceScene())
        .then((cleanup) => cleanups.push(cleanup))
        .catch(() => {
            document.querySelector('[data-scene-root]')?.classList.add('is-fallback');
        });
}

if (import.meta.hot) {
    import.meta.hot.dispose(() => {
        cleanups.forEach((cleanup) => cleanup?.());
    });
}
