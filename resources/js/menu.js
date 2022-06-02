window.onload = function () {
    let path = window.location.pathname;
    const pathArray = path.split('/');
    let last = pathArray[pathArray.length - 1];

    if (last === '' || last === 'index.php') {
        document.getElementById('navHome').classList.add('active');
        return;
    }

    const allowedArray = ['Nayeon', 'Jeongyeon', 'Momo', 'Sana', 'Jihyo', 'Mina', 'Dahyun', 'Chaeyoung', 'forming', 'discography', 'jype', 'manage'];
    const exeptionArray = ['forming', 'discography', 'jype'];
    const nameArray = last.split('.');
    let name = nameArray[0];


    if (allowedArray.includes(name)) {
        if (!exeptionArray.includes(name)) {
            document.getElementById('nav' + name).classList.add('active');
            return;
        } else {
            if (name === 'forming') {
                document.getElementById('navHistory').classList.add('active');
                return;
            } else {
                document.getElementById('nav' + name.charAt(0).toUpperCase() + name.slice(1)).classList.add('active');
                return;
            }
        }
    }


    console.log(name);
}