function DownloadAPK() {
  let url = "./public/offauth-beta-1.0.0.apk";
  window.location = url;
}


window.onload = function(){
  const modals = document.querySelectorAll("div.modal-box");
  const modalHandlerOpen = document.querySelectorAll("#app-gallery > p.small-text > a.modal-link");
  const modalHandlerClose = document.querySelectorAll("span.close");

  if(modalHandlerOpen && modals.length > 0) {
    window.addEventListener("click", function(event) {
      modals.forEach((item, i) => {
        if(item.style.display === "block" && !item.contains(event.target.parentNode)) {
          item.style.display = "none";
        }
        else if (event.target == modalHandlerClose[i]) {
          item.style.display = "none";
        }
        else if (event.target == modalHandlerOpen[i]) {
          item.style.display = "block";
        }
      });
    });
  }

}
