"use strict";

$(document).ready(function()
{
    $.post('../../../pks_business_partner/get_by_list', {list:$('#userCabang').val()}, (data) =>
    {
        const option = JSON.parse(data)
        if (option.data)
        {
            option.data.forEach((val) =>
            {						
                $("<option />", {value:val.id, text:`${val.nama} - ${val.pks_askrindo}`}).appendTo($('#downloadparam'))
            })
        }
    })
})
