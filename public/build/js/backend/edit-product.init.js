 //* choices category input
var productCategoryInput = new Choices('#choices-category-input', {
    searchEnabled: false,
    shouldSort: false,
});

var forms = document.querySelectorAll('.needs-validation')


Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
        $('#invalidcodprod').html('C&oacute;digo');
        $('#invalidcodprod').removeClass('text-danger');

        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        } else {
            event.preventDefault();

            var productCategoryValue = productCategoryInput.getValue(true);

            var codprod    = document.getElementById("codprod").value;
            var descrip    = document.getElementById("descrip").value;
            var descrip2   = document.getElementById("descrip2").value;
            var descrip3   = document.getElementById("descrip3").value;

            var refere     = document.getElementById("refere").value;
            var marca      = document.getElementById("marca").value;
            var codinst    = productCategoryInput.getValue(true);
            var isadmin    = document.getElementById("isadmin").value;

            if(isadmin == 1){
                var preciod    = document.getElementById("preciod").value;
                var costod     = document.getElementById("costod").value;
                var costod2    = document.getElementById("costod2").value;
                var costod3    = document.getElementById("costod3").value;
                var esexento   = document.getElementById("esexento").value;
            }

            var exdecimal  = document.getElementById("exdecimal").value;
            var unidad     = document.getElementById("unidad").value;

            var formAction = document.getElementById("formAction").value;

            if (formAction == "edit" && productCategoryValue !== ""  ) {
                document.getElementById("editproduct-form").submit();
            }else {
                console.log('Form Action Not Found.');
            }

            return false;
        }

        form.classList.add('was-validated');

    }, false)
});
