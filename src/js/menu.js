const userMenuButton = document.getElementById("user-menu-button");
const userMenu = document.getElementById("user-menu");
if (userMenuButton && userMenu) {
  userMenuButton.addEventListener("click", () => {
    userMenu.classList.toggle("hidden");
    if (!userMenu.classList.contains("hidden")) {
      userMenu.classList.remove("animate__fadeOutUp");
      userMenu.classList.add("animate__fadeInDownBig");
      setTimeout(() => {
        userMenu.classList.add("menu-enter-active");
      }, 10);
    } else {
      userMenu.classList.remove("animate__fadeInDownBig");
      userMenu.classList.add("animate__fadeOutUp");
      userMenu.classList.remove("menu-enter-active");
    }
  });
}

// Close user menu when clicking outside
document.addEventListener("click", (event) => {
  if (
    userMenuButton &&
    userMenu &&
    !userMenuButton.contains(event.target) &&
    !userMenu.contains(event.target)
  ) {
    userMenu.classList.add("hidden");
    userMenu.classList.remove("animate__fadeInDownBig");
    userMenu.classList.remove("menu-enter-active");
    userMenu.classList.add("animate__fadeOutUp");
  }
});
