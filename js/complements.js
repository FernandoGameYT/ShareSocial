$(document.body).append(`<div class="loader" id="loader">
<div class="spinner-border text-primary" role="status"></div>
</div>`);
const loaderElement = $("#loader");

const loader = {
    active: () => {
        loaderElement.addClass("active");
    },
    desactive: () => {
        loaderElement.removeClass("active");
    }
}

const alerts = {
    add: (type, content) => {
        $(".alert").remove();
        
        if(type == "error") {
            type = "danger";
            title = "Error:"
        }else if(type == "success") {
            title = "Exito";
        }
        $(document.body).append(`<div class="alert alert-${type}" role="alert">
            <strong>${title}</strong> ${content}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>`);
    }
}