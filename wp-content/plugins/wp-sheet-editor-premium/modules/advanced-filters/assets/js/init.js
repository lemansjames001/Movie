jQuery(document).ready(function(){var d=jQuery("#be-filters");if(!d.length)return!0;var e=d.find(".advanced-filters .base");e.removeClass("base"),window.advancedGroupTemplate=e[0].outerHTML,e.remove(),d.on("click",".remove-advanced-filter",function(e){e.preventDefault();var a=jQuery(this).parents("li"),t=a.find(".wpse-advanced-filters-field-selector").attr("name");beAddRowsFilter(t+"="),a.remove()}),jQuery("body").on("click","#be-filters .new-advanced-filter",function(e){e.preventDefault();var a=window.advancedGroupTemplate.replace(new RegExp(/\[\]/,"g"),"["+(d.find(".advanced-filters .advanced-filters-list").children().length+1)+"]"),t=d.find(".advanced-filters  .advanced-filters-list .advanced-field").last();t.length?t.after(a):d.find(".advanced-filters  .advanced-filters-list").prepend(a),vgseInitSelect2(d.find(".advanced-filters  .advanced-filters-list .advanced-field select.select2").last()),d.find(".advanced-filters  .advanced-filters-list").children().show()}),d.find(".new-advanced-filter").trigger("click"),d.find(".advanced-filters-toggle").change(function(){jQuery(this).is(":checked")?d.find(".advanced-filters").show():d.find(".advanced-filters").hide()}),jQuery("body").on("change",".wpse-advanced-filters-field-selector",function(){var e=jQuery(this).val(),a=jQuery(this);if(!e||e&&a.data("last-formatted-key")&&e!==a.data("last-formatted-key")){var t=a.attr("name").replace("[key]","[value]");a.parents(".fields-wrap").find(".wpse-advanced-filters-value-selector:gt(0)").remove(),a.parents(".fields-wrap").find(".wpse-advanced-filters-value-selector").replaceWith('<input name="'+t+'" class="wpse-advanced-filters-value-selector" />'),a.data("last-formatted-key","")}e&&(a.parent().find("input.field-source").val(a.find("option:selected").data("source")),a.parent().find(".search-value-wrap input").val(""),vgseInputToFormattedColumnField(e,a.parents(".fields-wrap"),".wpse-advanced-filters-value-selector"),a.data("last-formatted-key",e))})}),jQuery(document).ready(function(){jQuery("body").on("click","[data-start-saved-search]",function(e){e.preventDefault();var a=jQuery(this).data("start-saved-search");vgseRemoveAllFilters(),jQuery("body").data("be-filters",""),beAddRowsFilter(a),vgseReloadSpreadsheet()}),jQuery("[data-start-saved-search]").each(function(){jQuery(this).after('<button type="button" class="wpse-delete-saved-search">x</button>')}),jQuery("body").on("click",".wpse-delete-saved-search",function(e){e.preventDefault();var a=jQuery(this),t={nonce:jQuery("#vgse-wrapper").data("nonce"),post_type:jQuery("#post-data").data("post-type"),action:"vgse_delete_saved_search",search_name:a.prev().data("search-name")};a.parent().remove(),jQuery.post(ajaxurl,t,function(e){})})});