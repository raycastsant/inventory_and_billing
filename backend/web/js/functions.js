function setInputCursor(el) {
    if (el !== null) {
        var caretPos = el.value.length;
        if (el.createTextRange) {
            var range = el.createTextRange();
            range.move('character', caretPos);
            range.select();
        }
        else {
            // (el.selectionStart === 0 added for Firefox bug)
            if (el.selectionStart || el.selectionStart === 0) {
                el.focus();
                el.setSelectionRange(caretPos, caretPos);
            }
            else { 
                el.focus();
            }
        }
    }
}

function getEsDatatableConfig() {
    return  {
        "processing" : true,
        "serverSide" : false,
        "order" : [],
        "language" : {
            "processing"	:  "Procesando...",
            "lengthMenu"	: "Filas _MENU_",
            "info"		  : "Mostrando _START_ - _END_ de _TOTAL_ registros",
            "infoEmpty"	 : "Mostrando 0 - 0 de 0 registros",
            "infoFiltered"  : "(Filtrado de _MAX_ registros)",
            "infoPostFix"   : "",
            "search"   : "Buscar",
            "loadingRecords": "Cargando...",
            "zeroRecords"   : "No se encontraron resultados",
            "emptyTable"	: "No hay datos",
            "bSort":true,
            "paginate" : {
                "first"	 : "Primero",
                "previous"  : "«",
                "next"	  : "»",
                "last"	  : "Último",
            },
        },
    }
}

// Para acomodar el filtro de seleccionar numero de paginas a mostrar en un DataTable 
/*function acomodarFiltrosDataTable(tableId) {
    var label = $('#' + tableId + '_length label')[0];
    if (label != null) {
        
        var div = $('#' + tableId + '_length')[0];
        var select = label.control;
        
        label.innerHTML = "Filas" + select.outerHTML;
        label.control = select;
        console.log(label);
    }
}*/