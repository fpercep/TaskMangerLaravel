export default (modalName, extraData = {}) => ({
    show: false,
    ...extraData,
    handleOpen(event) {
        if (event.detail.name === modalName) {
            this.show = true;
            if(event.detail.payload) {
                Object.assign(this, event.detail.payload);
            }
            if (this.onOpen) this.onOpen();
        }
    },
    handleClose(event) {
        if (!event || event.detail.name === modalName) {
            this.show = false;
        }
    }
});
