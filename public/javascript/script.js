const _elements = {
    toggle: document.querySelector(".nav-header__toggle"),
    sidebar: document.querySelector(".sidebar"),
    sidebar__close: document.querySelector(".sidebar__close"),
};

_elements.toggle.addEventListener("click", () => {
    _elements.sidebar.classList.add("sidebar--show");
    _elements.toggle.classList.add("nav-header__toggle--hide");
});

_elements.sidebar__close.addEventListener("click", () => {
    _elements.sidebar.classList.remove("sidebar--show");
    _elements.toggle.classList.remove("nav-header__toggle--hide");
});