/* Darkmode */

let darkmode = localStorage.getItem('darkmode')
const themeSwitch = document.getElementById('theme-switch')


const enableDarkmode = () => {
    document.body.classList.add('darkmode')
    localStorage.setItem('darkmode', 'active')
}

const disableDarkmode = () => {
    document.body.classList.remove('darkmode')
    localStorage.setItem('darkmode', null)
}

if (darkmode === "active") enableDarkmode()

themeSwitch.addEventListener("click", () => {
    darkmode = localStorage.getItem('darkmode')
    darkmode !== "active" ? enableDarkmode() : disableDarkmode()
})

/* Hamburger Menu*/

const hamburgerMenu = document.querySelector(".hamburger")

const navMenu = document.querySelector(".headerList")

hamburgerMenu.addEventListener("click", () =>{
    hamburgerMenu.classList.toggle('active');
    navMenu.classList.toggle('active');
    document.body.classList.toggle('no-scroll')
})

document.querySelectorAll("headerMenuLink").forEach(n => n.addEventListener("click", () => {
    hamburgerMenu.classList.remove('active')
    navMenu.classList.remove('active')
    document.body.classList.remove('no-scroll')
})
)