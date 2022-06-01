let elem, elemChild;

window.onload = function () {
    // document.getElementById('albumSubMenu').style.display = "none";
    // document.getElementById('songSubMenu').style.display = "none";
    // document.getElementById('albumSubMenu').style.visibility = "hidden";
}

function changeVisibilityOfChildElement(elemId, elemChildId) {
    elem = document.getElementById(elemId);
    elemChild = document.getElementById(elemChildId);
    if (elem.checked) {
        elemChild.style.display = "block";
    } else {
        elemChild.style.display = "none";
    }
}