// add nad remove class to body when gutenberg sidebar is opened or closed
if (wp && wp.data && jQuery('.block-editor-page').length) {
    const select = wp.data.select('core/edit-post');
    let previousState = null;

    const checkSidebarState = () => {
        const activePanel = select.getActiveGeneralSidebarName();
        const isOpened = Boolean(activePanel);

        // Оновлюємо клас тільки якщо стан змінився
        if (previousState !== isOpened) {
            document.body.classList.toggle('is-gutenberg-sidebar-opened', isOpened);
            previousState = isOpened;
            //console.log('Стан сайдбару:', isOpened ? 'відкритий' : 'закритий');
        }
    };

    wp.data.subscribe(checkSidebarState);
    checkSidebarState(); // Перевіряємо початковий стан
}
