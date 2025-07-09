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

  const filterForm = document.getElementById("bsf-sidebar-filter");
  const storageKey = "bsfEventFiltersState";
  const pastButton = document.getElementById("bsf-show-past-events");

  function saveFilters() {
    if (!filterForm) return;
    const data = {};
    const formData = new FormData(filterForm);
    for (const [key, value] of formData.entries()) {
      if (data[key]) {
        if (!Array.isArray(data[key])) data[key] = [data[key]];
        data[key].push(value);
      } else {
        data[key] = value;
      }
    }
    sessionStorage.setItem(storageKey, JSON.stringify(data));
  }

  function restoreFilters() {
    if (!filterForm) return;
    const saved = sessionStorage.getItem(storageKey);
    if (!saved) return;
    try {
      const data = JSON.parse(saved);
      Object.keys(data).forEach(name => {
        const values = Array.isArray(data[name]) ? data[name] : [data[name]];
        values.forEach(v => {
          const field = filterForm.querySelector(`[name="${name}"][value="${v}"]`);
          if (field) field.checked = true;
          const input = filterForm.querySelector(`[name="${name}"]`);
          if (input && input.type === "text") input.value = v;
        });
      });
      htmx.trigger(filterForm, "change");
    } catch (e) {}
  }

  function hidePastEvents() {
    const now = new Date();
    let hasHidden = false;
    document.querySelectorAll(".bsf-event-card").forEach(card => {
      const end = card.dataset.end;
      if (end) {
        const endTime = new Date(end);
        if (endTime < now) {
          card.classList.add("bsf-hidden-past");
          hasHidden = true;
        }
      }
    });
    if (pastButton && hasHidden) pastButton.style.display = "block";
  }

  if (filterForm) {
    filterForm.addEventListener("change", saveFilters);
    restoreFilters();
  }

  if (pastButton) {
    pastButton.addEventListener("click", function () {
      document.querySelectorAll(".bsf-hidden-past").forEach(card => card.classList.remove("bsf-hidden-past"));
      pastButton.style.display = "none";
    });
  }

  document.body.addEventListener("htmx:afterSwap", hidePastEvents);
  hidePastEvents();
});

