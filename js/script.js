function ShowForm(formId){
        document.querySelectorAll(".formBox").forEach(form => form.classList.remove("active"));
        document.getElementById(formId).classList.add("active");
        
        
        }