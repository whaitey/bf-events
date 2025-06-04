document.addEventListener("DOMContentLoaded", function () {
  //console.log("Custom plugin script loaded.");

  // Dropdown filter
    document.querySelectorAll(".dropdown-filter-label").forEach(button => {
        button.addEventListener("click", function () {
            const inputList = this.nextElementSibling;
            if (inputList && inputList.classList.contains("input-list")) {
                if (inputList.style.maxHeight) {
                    inputList.style.maxHeight = null;
                    this.classList.remove("active");
                } else {
                    inputList.style.maxHeight = inputList.scrollHeight + "px";
                    this.classList.add("active");
                }
            }
        });
    });

     // Filters Modal
     const filtersModal = document.getElementById("bsf-filters-main-parent");
     const filtersOpenButton = document.querySelector(".bsf-show-filters-button");
     const filtersCloseButton = document.querySelector(".bsf-apply-filters-button");
     const filtersXcloseButton = document.getElementById("filters-mobile-close");
 
     if (filtersModal) {
         if (filtersOpenButton) {
             filtersOpenButton.addEventListener("click", () => filtersModal.classList.add("show"));
         }
 
         if (filtersCloseButton) {
             filtersCloseButton.addEventListener("click", () => filtersModal.classList.remove("show"));
         }
 
         if (filtersXcloseButton) {
            filtersXcloseButton.addEventListener("click", () => filtersModal.classList.remove("show"));
         }
     }
 
     // event modal
     document.addEventListener("click", function (event) {
         const eventModal = document.getElementById("bsf-modal");
         const eventModalContent = document.getElementById("bsf-modal-content");
 
         if (eventModal && eventModal.classList.contains("show") && !eventModalContent.contains(event.target)) {
            
             eventModal.classList.remove("show");
         }
     });



  

  document.body.addEventListener("htmx:afterSwap", function (event) {
      //console.log("HTMX content updated.");

    // load event modal on the calendar page
    if (event.detail.target.id === "bsf-modal-content") {
        document.getElementById("bsf-modal").classList.add("show");
    }
  });
});

