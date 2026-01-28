window.addEventListener("DOMContentLoaded", function () {
    console.log("DOM LOADED");
    if (window.Echo) {
        console.log("Echo loaded");
        window.Echo.private(`App.Models.User.${window.uid}`).notification(
            (notification) => {
                console.log("Enter!", notification);
            },
        );
    } else {
        console.error("Echo not loaded!");
    }
});
