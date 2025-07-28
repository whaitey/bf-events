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
  const pastCheckbox = document.getElementById("bsf-show-past-events");

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
    // Check if we're on a speaker page - if so, don't hide past events
    const isSpeakerPage = document.querySelector('.bsf-single-speaker-content-outer-wrapper');
    if (isSpeakerPage) {
      return; // Don't hide past events on speaker pages
    }

    const showPast = pastCheckbox && pastCheckbox.checked;
    const now = new Date();
    document.querySelectorAll(".bsf-event-card").forEach(card => {
      const end = card.dataset.end;
      if (end) {
        const endTime = new Date(end);
        if (endTime < now) {
          if (showPast) {
            card.classList.remove("bsf-hidden-past");
          } else {
            card.classList.add("bsf-hidden-past");
          }
        } else {
          card.classList.remove("bsf-hidden-past");
        }
      }
    });
  }

  if (filterForm) {
    filterForm.addEventListener("change", saveFilters);
    restoreFilters();
  }

  if (pastCheckbox) {
    pastCheckbox.addEventListener("change", hidePastEvents);
  }

  document.body.addEventListener("htmx:afterSwap", hidePastEvents);
  hidePastEvents();

  // Dynamic filter functionality
  function updateDynamicFilters() {
    if (!filterForm) return;

    const selectedEvents = [];
    
    // Get selected event names (radio buttons)
    const eventRadios = filterForm.querySelectorAll('input[name="eventNamesArray[]"]:checked');
    eventRadios.forEach(radio => {
      if (radio.value !== '') { // Don't include "Összes" (All) option
        selectedEvents.push(radio.value);
      }
    });

    console.log('Selected events:', selectedEvents); // Debug

    // Prepare data for AJAX request
    const data = new FormData();
    data.append('action', 'bsf_get_dynamic_filter_options');
    data.append('nonce', bsfEventsAjax.nonce || '');
    
    selectedEvents.forEach(eventId => {
      data.append('eventNamesArray[]', eventId);
    });

    console.log('Sending AJAX request...'); // Debug

    // Make AJAX request
    fetch(bsfEventsAjax.ajax_url, {
      method: 'POST',
      body: data
    })
    .then(response => response.json())
    .then(data => {
      console.log('AJAX response:', data); // Debug
      if (data.success) {
        updateFilterOptions(data.data);
      }
    })
    .catch(error => {
      console.error('Error updating dynamic filters:', error);
    });
  }

  function updateFilterOptions(options) {
    console.log('Updating filter options with:', options); // Debug
    
    // Update stages
    updateDropdownOptions('bsf-stage-dropdown', options.stages, 'stagesArray[]');
    
    // Update locations
    updateDropdownOptions('bsf-location-dropdown', options.locations, 'locationsArray[]');
    
    // Update tags
    updateTagOptions(options.tags);
    
    // Update speakers
    updateDropdownOptions('bsf-speaker-dropdown', options.speakers, 'speakersArray[]');
    
    // Update companies
    updateCompanyOptions(options.companies);
  }

  function updateDropdownOptions(dropdownId, options, inputName) {
    console.log(`Updating dropdown ${dropdownId} with ${options.length} options`); // Debug
    
    const dropdown = document.getElementById(dropdownId);
    if (!dropdown) {
      console.log(`Dropdown ${dropdownId} not found`); // Debug
      return;
    }

    const inputList = dropdown.querySelector('.input-list-inner-wrapper');
    if (!inputList) {
      console.log(`Input list not found in ${dropdownId}`); // Debug
      return;
    }

    // Clear existing options (except search input)
    const searchInput = inputList.querySelector('.dropdown-search');
    inputList.innerHTML = '';
    if (searchInput) {
      inputList.appendChild(searchInput);
    }

    // Add new options
    options.forEach(option => {
      const line = document.createElement('div');
      line.className = 'input-line checkbox-line';
      
      const label = document.createElement('label');
      const name = option.name || option.post_title || option;
      label.textContent = name;
      
      const input = document.createElement('input');
      input.type = 'checkbox';
      input.name = inputName;
      input.value = option.term_id || option.ID || option;
      
      const checkmark = document.createElement('span');
      checkmark.className = 'checkmark';
      
      label.appendChild(input);
      label.appendChild(checkmark);
      line.appendChild(label);
      inputList.appendChild(line);
    });
  }

  function updateTagOptions(tags) {
    const tagWrapper = document.querySelector('.bsf-tag-filters-wrapper');
    if (!tagWrapper) return;

    // Clear existing tags
    tagWrapper.innerHTML = '';

    // Add new tags
    tags.forEach(tag => {
      const input = document.createElement('input');
      input.type = 'checkbox';
      input.name = 'tagsArray[]';
      input.value = tag.term_id;
      input.id = `bsf-tag-input-${tag.term_id}`;
      
      const label = document.createElement('label');
      label.className = 'bsf-event-tag bsf-button small outline-black';
      label.setAttribute('for', `bsf-tag-input-${tag.term_id}`);
      label.textContent = tag.name;
      
      tagWrapper.appendChild(input);
      tagWrapper.appendChild(label);
    });
  }

  function updateCompanyOptions(companies) {
    const companyDropdown = document.getElementById('bsf-company-dropdown');
    if (!companyDropdown) return;

    const inputList = companyDropdown.querySelector('.input-list-inner-wrapper');
    if (!inputList) return;

    // Clear existing options (except search input)
    const searchInput = inputList.querySelector('.dropdown-search');
    inputList.innerHTML = '';
    if (searchInput) {
      inputList.appendChild(searchInput);
    }

    // Add new options
    companies.forEach(company => {
      const line = document.createElement('div');
      line.className = 'input-line checkbox-line';
      
      const label = document.createElement('label');
      label.textContent = company;
      
      const input = document.createElement('input');
      input.type = 'checkbox';
      input.name = 'companiesArray[]';
      input.value = company;
      
      const checkmark = document.createElement('span');
      checkmark.className = 'checkmark';
      
      label.appendChild(input);
      label.appendChild(checkmark);
      line.appendChild(label);
      inputList.appendChild(line);
    });
  }

  // Add event listeners for dynamic filtering
  if (filterForm) {
    const eventRadios = filterForm.querySelectorAll('input[name="eventNamesArray[]"]');
    eventRadios.forEach(radio => {
      radio.addEventListener('change', updateDynamicFilters);
    });
  }
});

