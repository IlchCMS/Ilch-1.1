/* 
 * Add Reload Button for Captcha Images
 */
(function() {
    var img, imgs = document.getElementsByTagName('img');
    for (var i = 0, l = imgs.length; i < l; i++) {
        img = imgs[i];
        if (img.className.match(/\bcaptchaImage\b/)) {
            appendReloadButton(img);
        }
    }

    function appendReloadButton(image)
    {
        var img = document.createElement('img');
        img.src = 'include/images/icons/reload.gif';
        img.title = 'Captcha neu generieren';
        img.alt = 'reload';
        img.style.cursor = 'pointer';
        img.onclick = function() { reloadImage(image); };
        image.parentNode.insertBefore(img, image.nextSibling);
    }

    function getRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function reloadImage(image) {
        var src = image.src, newSrc, newRandomId = '';

        result = /captchaimg.php\?id=(\w+)_(.+)&nocache=(\d+)/.exec(src);
        if (result) {
            for (var i = 0, length = result[2].length; i < length; i++) {
                newRandomId += getRandomInt(0, 9);
            }
            newSrc = src.replace(result[2], newRandomId).replace(result[3], Date.now());
            image.src = newSrc;
        }
    }
})();


