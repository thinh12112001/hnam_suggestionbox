const body = document.querySelector('body');
const bgOverlay = document.getElementById('bg-overlay');
const deviceWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;



// Handle scroll nav
const eleScroll = document.querySelector(".nav-main");
const header = document.querySelector('header');
const hNavMain = eleScroll.offsetHeight;
const spaceTop = eleScroll.offsetTop;
window.addEventListener('scroll', function () {
    let scrollPos = window.scrollY;
    if (scrollPos >= (spaceTop + hNavMain + 200)) {
        eleScroll.classList.add('scroll');
        header.style.marginBottom = hNavMain + 'px';
    } else {
        eleScroll.classList.remove('scroll');
        header.style.marginBottom = '0';
    }
});

// Nav mobile
const navMB = document.getElementById('nav-mb');
const buttonNav = document.getElementById('mb-nav-button');
const closeNav = navMB.querySelector('.js-close-nav-mb');
function showNavMB() {
    // navMB.classList.add('show');
    // bgOverlay.classList.add('show');
    // body.style.overflow = 'hidden';
    navMB.classList.toggle('show');
}
function hideNavMB() {
    // navMB.classList.remove('show');
    // bgOverlay.classList.remove('show');
    // body.style.overflow = null;

    navMB.classList.toggle('show')
}
buttonNav.addEventListener('click', function (e) {
    // e.preventDefault();
    // showNavMB();

    e.preventDefault();
    hideNavMB();
});
// closeNav.addEventListener('click', function (e) {
//     e.preventDefault();
//     hideNavMB();
// });
// document.getElementById('bg-overlay').addEventListener('click', function () {
//     hideNavMB();
// });
document.getElementById('menu-search-button').addEventListener('click', function () {
    document.getElementById('menu-search').classList.toggle('show');
});

function isEmptyObject(obj) {
    return JSON.stringify(obj) == '{}';
}
function loadJS(obj) {
    if (obj.src !== undefined && obj.src != '') {
        var newJS;
        newJS = document.createElement('script');
        if (obj.async === true) {
            newJS.async = true;
        }
        if (obj.defer === true) {
            newJS.defer = true;
        }
        if (obj.crossorigin != undefined) {
            newJS.crossorigin = obj.crossorigin;
        }
        if (isFunction(obj.onload)) {
            newJS.onload = obj.onload;
        }
        newJS.src = obj.src;
        document.head.appendChild(newJS);
    }
}
function loadCSS(obj) {
    if (obj.src !== undefined && obj.src != '') {

        var link  = document.createElement('link');
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = obj.src;
        link.media = 'all';
        document.head.appendChild(link);
    }
}
function isFunction(possibleFunction) {
    return typeof(possibleFunction) === typeof(Function);
}



// let back_menu2 = document.querySelectorAll('.primary-ul .has-sub .sub-menu .has-sub');
// // for (let i = 0; i < back_menu.length; i++) {
// //     back_menu[i].addEventListener('click', function (event) {
// //
// //         // console.log(event.target.classList);
// //         // console.log(event.target.parentElement.classList);
// //         if (event.target.classList.contains("back-menu")){
// //             // const boxDropdown = event.target.closest('ul');
// //             let boxDropdown = event.target.parentElement.previousElementSibling;
// //             boxDropdown.classList.remove('show');
// //             // boxDropdown.previousElementSibling.querySelector(".sub-menu").classList.remove('show');
// //             // console.log(boxDropdown[0]);
// //         }
// //         if(event.target.classList.contains("has-sub") || event.target.parentElement.classList.contains("has-sub")){
// //             event.preventDefault();
// //             console.log('vv');
// //
// //             let boxDropdown = this.querySelector('.sub-menu');
// //             boxDropdown.classList.add('show');
// //         }
// //
// //     })
// // }

//menu mobile


const pageHeader = document.querySelector(".nav-main");
const toggleMenu = pageHeader.querySelector("#mb-nav-button");
const menuWrapper = pageHeader.querySelector(".box-2");
const level1Links = pageHeader.querySelectorAll(".primary-ul > li > a");
const listWrapper2 = pageHeader.querySelector(".menu-wrap:nth-child(3)");
const listWrapper3 = pageHeader.querySelector(".menu-wrap:nth-child(4)");
const subMenuWrapper2 = listWrapper2.querySelector(".sub-menu-wrapper");
const subMenuWrapper3 = listWrapper3.querySelector(".sub-menu-wrapper");
const backOneLevelBtns = pageHeader.querySelectorAll(".back-one-level");
const isVisibleClass = "is-visible";
const isActiveClass = "is-active";

toggleMenu.addEventListener("click", function () {
    let width = screen.width;
    if (width < 678){
        menuWrapper.classList.toggle(isVisibleClass);
        if (!this.classList.contains(isVisibleClass)) {
            listWrapper2.classList.remove(isVisibleClass);
            listWrapper3.classList.remove(isVisibleClass);
            const menuLinks = menuWrapper.querySelectorAll("a");
            for (const menuLink of menuLinks) {
                menuLink.classList.remove(isActiveClass);
            }
        }
    }

});

for (const level1Link of level1Links) {
    level1Link.addEventListener("click", function (e) {
        let width = screen.width;
        if (width < 678){
            const siblingList = level1Link.nextElementSibling;
            if (siblingList) {
                e.preventDefault();
                this.classList.add(isActiveClass);
                const cloneSiblingList = siblingList.cloneNode(true);
                subMenuWrapper2.innerHTML = "";
                subMenuWrapper2.append(cloneSiblingList);
                listWrapper2.classList.add(isVisibleClass);
            }
        }
    });
}

listWrapper2.addEventListener("click", function (e) {
    const target = e.target;
    if (target.tagName.toLowerCase() === "a" && target.nextElementSibling) {
        const siblingList = target.nextElementSibling;
        e.preventDefault();
        target.classList.add(isActiveClass);
        const cloneSiblingList = siblingList.cloneNode(true);
        subMenuWrapper3.innerHTML = "";
        subMenuWrapper3.append(cloneSiblingList);
        listWrapper3.classList.add(isVisibleClass);
    }
});

for (const backOneLevelBtn of backOneLevelBtns) {
    backOneLevelBtn.addEventListener("click", function () {
        const parent = this.closest(".menu-wrap");
        parent.classList.remove(isVisibleClass);
        parent.previousElementSibling
            .querySelector(".is-active")
            .classList.remove(isActiveClass);
    });
}