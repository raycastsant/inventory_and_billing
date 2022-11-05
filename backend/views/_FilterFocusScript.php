<?php 
use yii\helpers\Html;

/**Parte de una vista importada en la mayoria de las vistas INDEX, para reestablecer el cursor e 
 * implementar busquedas con el KeyPress*/

ob_start(); // output buffer the javascript to register later ?>
    <script>
       $(function() {
            setupGridView();

           // $.pjax.defaults.timeout = 10000;
        });

       //var lastVal;

        // Setup the filter(s) controls
        function setupGridView(grid) {
            if(grid==null) {
                grid = '.grid-view tr.filters';
                
                jQuery("#p0 .form-control").each(function() {
                    $('input[name="'+this.name+'"]').attr('data-pjax', '0');
                });
            }

            // Default handler for filter change event
            $('input,select', grid).change(function() {
                var grid = $(this).closest('.grid-view');
                $(document).data(grid.attr('id')+'-lastFocused', this.name);
            });
        }

        // Default handler for beforeAjaxUpdate event
        function afterAjaxUpdate(id) {
            var grid = $('#'+id);
            var lf = $(document).data(grid.attr('id')+'-lastFocused');
            
            //console.log(lf);

            // If the function was not activated
            if(lf == null) return;
            // Get the control
            fe = $('[name="'+lf+'"]', grid);
            // If the control exists..
            if(fe!=null) {
            //    fe.value = lastVal;
                
                if(fe.get(0).tagName == 'INPUT' && fe.attr('type') == 'text')
                    // Focus and place the cursor at the end
                    fe.cursorEnd();
                else
                    // Just focus
                    fe.focus();
            }
            // Setup the new filter controls
            setupGridView(grid);
        };

        // Place the cursor at the end of the text field
        jQuery.fn.cursorEnd = function() {
            return this.each(function() {
                if(this.setSelectionRange) {
                    this.focus();
                    this.setSelectionRange(this.value.length,this.value.length);
                }
                else 
                if (this.createTextRange) {
                    var range = this.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', this.value.length);
                    range.moveStart('character', this.value.length);
                    range.select();
                }
                return false;
            });
        }

        var clockresetIndex = 0;
        
        var secondPjaxResponse = false;
        jQuery("#p0").on("keyup", "input", function(e) {
         //  lastVal = $(this)[0].value;
            if(e.which !== 35 && e.which !== 36 && e.which !== 144 && e.which !== 33 && 
               e.which !== 34 && e.which !== 16 && e.which !== 9) {  //Teclas Home, end, Numlock, pgUp, pgDown, Shift, TAB
                
                    var cspan = $('#w0 table thead')[0].rows[0].cells.length;
                    $('#w0 table tbody').html("<tr><td colspan='"+cspan+"' style='text-align:center;'><h3>Procesando información&nbsp;<img src='/InvFactServices/backend/web/images/loader.gif' alt=''></h3></td></tr>");

                    jQuery(this).change();
            }

           /*$("#w0").yiiGridView('applyFilter', {
                data: $(this).serialize()
           });*/
           //return false;
        });

        /*jQuery("#p0").on("click", "input", function() {
            secondPjaxResponse = false;
        });*/

        //Cuando PJAX recargue establecer el cursor en la busqueda    
        //$("#p0").on("pjax:end", function(data, status, xhr) {   
        jQuery(document).on("pjax:success", "#p0", function(event) {

           /* console.log("secondPjaxResponse: "+secondPjaxResponse+"    focusSet: "+focusSet);
            if(secondPjaxResponse && !focusSet) {
                console.log("afterAjaxUpdate");
                afterAjaxUpdate("w0");
                focusSet = true;
            }
           
            secondPjaxResponse = !secondPjaxResponse;*/

            console.log("afterAjaxUpdate");
            afterAjaxUpdate("w0");

           /* if(clockresetIndex !== 0)
                clearTimeout(clockresetIndex);

                clockresetIndex = setTimeout(() => {
                    if(secondPjaxResponse) {
                        console.log("afterAjaxUpdate");
                        afterAjaxUpdate("w0");
                    }

                    secondPjaxResponse = !secondPjaxResponse;
            }, 100);*/
        });
    </script>
<?php $this->registerJs(str_replace(['<script>', '</script>'], '', ob_get_clean())); ?>