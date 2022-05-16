
function openImageGallery() {
    document.getElementById("Image_gallery").style.display = "block";
}

function closeImageGallery() {
    document.getElementById("Image_gallery").style.display = "none";
}

var slideIndex = 1;
showSlides(slideIndex);

function changeSlide(n) {
    showSlides(slideIndex += n);
}

function currentSlide(n) {
    showSlides(slideIndex = n);
}

function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("slide");
    // document.style.display = "none";
    // var dots = document.getElementsByClassName("demo");
    // var captionText = document.getElementById("caption");
    if (n > slides.length) {slideIndex = 1}

    if (n < 1) {slideIndex = slides.length}

    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    // for (i = 0; i < dots.length; i++) {
    //     dots[i].className = dots[i].className.replace(" active", "");
    // }
    slides[slideIndex-1].style.display = "flex";
    // dots[slideIndex-1].className += " active";
    // captionText.innerHTML = dots[slideIndex-1].alt;
}