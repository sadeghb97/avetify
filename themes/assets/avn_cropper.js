let lastCropper = null;
let increasingMode = true;
function setCropConfigs(event, imageId, ratio){
    const clickRelativeX = event.offsetX;
    const clickRelativeY = event.offsetY;

    const image = document.getElementById(imageId);
    const imageHeight = image.naturalHeight
    const imageWidth = image.naturalWidth
    const inpX = document.getElementById(imageId + "_x");
    const inpY = document.getElementById(imageId + "_y");
    const cropHeight = document.getElementById(imageId + "_h");
    const cropWidth = document.getElementById(imageId + "_w");
    const cropStatus = document.getElementById(imageId + "_status");
    const inpEnabled = document.getElementById(imageId + "_enabled");

    let status = ""
    const cropper = new Cropper(image, {
        aspectRatio: ratio,
        zoomable: false,
        viewMode: 1,
        crop(event) {
            const max_x = imageWidth - event.detail.width
            const max_y = imageHeight - event.detail.height
            let fx = event.detail.x > 0 ? event.detail.x : 0
            let fy = event.detail.y > 0 ? event.detail.y : 0
            if(fx > max_x) fx = max_x
            if(fy > max_y) fy = max_y

            inpX.value = fx;
            inpY.value = fy;
            //inpX2.value = fx + event.detail.width;
            //inpY2.value = fy + event.detail.height;
            cropWidth.value =  event.detail.width;
            cropHeight.value = event.detail.height;

            status = "(" + fx + "," + fy + " -> "
                + cropWidth.value + "," + cropHeight.value + ")"
        },
    });

    setTimeout(() => {
        const newWidth = 200;
        const newHeight = 200 / ratio
        lastCropper.setCropBoxData({
            left: clickRelativeX - (newWidth / 2),
            top: clickRelativeY - (newHeight / 2),
            width: newWidth,
            height: newHeight
        })
    }, 100)
    lastCropper = cropper;

    document.onkeydown = function (evt) {
        evt = evt || window.event;
        if (evt.keyCode === 27) {
            cropper.destroy()
        } else if (evt.keyCode === 13) {
            inpEnabled.value = 1
            cropStatus.innerText = status
            cropper.destroy()
        }
    };
}