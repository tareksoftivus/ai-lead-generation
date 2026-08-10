document.addEventListener("DOMContentLoaded", () => {
  const contactForm = document.querySelector("[data-crm-contact-form]");
  if (!contactForm) return;

  const storeAction = contactForm.action;
  const method = contactForm.querySelector("[data-crm-method]");
  const title = document.querySelector("[data-crm-contact-title]");
  const fields = {
    name: contactForm.querySelector('[name="name"]'),
    role: contactForm.querySelector('[name="role"]'),
    email: contactForm.querySelector('[name="email"]'),
    phone: contactForm.querySelector('[name="phone"]'),
    note: contactForm.querySelector('[name="note"]'),
    lead: contactForm.querySelector('[name="lead_id"]'),
    primary: contactForm.querySelector('[name="is_primary"]'),
  };

  function setField(field, value) {
    if (field) field.value = value || "";
  }

  function reset() {
    contactForm.action = storeAction;
    if (method) method.value = "post";
    title && (title.textContent = "Add a contact");
    contactForm.reset();
    if (fields.lead) fields.lead.disabled = false;
  }

  function edit(trigger) {
    contactForm.action = trigger.dataset.action || storeAction;
    if (method) method.value = "patch";
    title && (title.textContent = "Edit contact");

    setField(fields.name, trigger.dataset.name);
    setField(fields.role, trigger.dataset.role);
    setField(fields.email, trigger.dataset.email);
    setField(fields.phone, trigger.dataset.phone);
    setField(fields.note, trigger.dataset.note);
    setField(fields.lead, trigger.dataset.leadId);
    if (fields.primary) fields.primary.checked = trigger.dataset.primary === "1";
    if (fields.lead) fields.lead.disabled = true;
  }

  document.addEventListener("click", (event) => {
    const opener = event.target.closest('[data-modal-open="contactModal"]');
    if (!opener) return;

    if (opener.hasAttribute("data-contact-edit")) {
      edit(opener);
    } else {
      reset();
    }
  });
});
