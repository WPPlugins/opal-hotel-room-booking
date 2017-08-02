/**
 * Created by DELL on 8/30/2016.
 */
jQuery(document).ready(function($){

    $('.fa-icon-picker').fontIconPicker({});

    var etfields_container = $(".opalhotel-creator-custom-fields .content-fields");

    etfields_container.on("click", ".opalopalhotel-remove-option", function(e){
        e.preventDefault();
        var remove_element_container = $(this).closest(".options-container");
        $(this).closest(".option-row").remove();

        remove_element_container.find("input.opalopalhotel-options-default").each(function(index){
            console.log(index);
            $(this).val(index);
        });

    });

    etfields_container.on('click', '.panel-title', function(e){
        e.preventDefault();
        $(this).closest(".panel-group").find(".panel-body").slideToggle();
    });

    etfields_container.on("click", ".remove-custom-field-item", function(e){
        e.preventDefault();
        $(this).closest(".panel-group").remove();

        $('.select-container').each(function (index, value) {

            $(this).find('input.opalopalhotel-options-label').attr("name", "opal_custom_select_options_label["+index+"][]");
            $(this).find('input.opalopalhotel-options-value').attr("name", "opal_custom_select_options_value["+index+"][]");
            $(this).find('input.opalopalhotel-options-default').attr("name", "opal_custom_select_options_default["+index+"][]");
            $(this).find('input.opalopalhotel-select-index').val(index);

        });
    });


    $(".create-et-field-btn").on("click", function(e){
        e.preventDefault();

        var nonce = $(this).attr("data-nonce");
        var type_field = $(this).data('type');

        $.ajax({
            type : "post",
            dataType : "json",
            url : OpalListing.ajaxurl,
            data : {action: "creator_custom_type", type : type_field, nonce: nonce},
            success: function(response) {
                if(response.type == "success") {
                    if(type_field == 'select'){
                        var index_select = $("input.opalopalhotel-select-index").length;
                        etfields_container.append(response.html);
                        $(".select-container:last").find("input.opalopalhotel-select-index").val(index_select);
                    }else{
                        etfields_container.append(response.html);
                    }
                    $('.fa-icon-picker').fontIconPicker({});
                }
                else {
                    alert("Error please try again");
                }
            }
        });
    });


    etfields_container.on("click", ".add-new-options", function(e){
        e.preventDefault();

        var option_container = $(this).closest('.select-container').find(".options-container");

        var add_new_option = $(this);

        var index = $(this).closest('.select-container').find("input.opalopalhotel-select-index").val();

        var option_index = $(this).closest('.select-container').find("input.opalopalhotel-options-default").length;

        var checked_default = '';
        if(option_index == 0){
            checked_default = 'checked';
        }

        $.ajax({
            type : 'POST',
            dataType : 'json',
            url : OpalListing.ajaxurl,
            data : {
                action: 'opalhotel_create_option_select',
                index : index,
                checked_default : checked_default,
                option_index: option_index
            },
            success: function(response) {
                if(response.type == "success") {
                    var option_html = response.html;
                    $(option_html).insertBefore(add_new_option);
                }
                else {
                    alert("Error please try again");
                }
            }
        });


    });
});
