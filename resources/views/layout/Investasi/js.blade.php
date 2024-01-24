<script>
    const authEdit = "<?php echo $edit;?>"
    var urlpath ={
        select: "{{ route('investasi_api.index') }}",
        update: "{{ route('investasi_api.update', ['investasi_api' => ':id']) }}",
        insert: "{{ route('investasi_api.store') }}",
        show: "{{ route('investasi_api.show', ['investasi_api' => ':id']) }}",
        delete: "{{ route('investasi_api.destroy', ['investasi_api' => ':id']) }}",
        showSubdata: "{{ route('investasi_subdata_api.show', ['investasi_subdata_api' => ':id']) }}",
        updateSubdata: "{{ route('investasi_subdata_api.update', ['investasi_subdata_api' => ':id']) }}",
    }

    $(function(){
        $('#investasi_tahun_table').val(new Date().getFullYear())
        onInitTable();
    })

    onInitTable = () =>{
        $.ajax({
            url: urlpath.select,
            type: 'GET',
            data: {
                year: $('#investasi_tahun_table').val()
            },
            success: function(response){
                if(response.status == true){
                    var array = []
                    $('#table_body').html('')
                    if(response.data.length){
                        $.each( response.data, function( k, v ){
                            let buttonEdit = (authEdit? `<a href="javascript:onEdit('${v.investasi_id}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"><span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/edit-icon.svg')) !!}</span></a><a href="javascript:onDelete('${v.investasi_id}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"><span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/delete-icon.svg')) !!}</span></a>`:``)

                            let html_action = ` <td class="text-center"><a href="javascript:onDetail('${v.investasi_id}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"><span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/detail-icon.svg')) !!}</span> </a>${buttonEdit}</td> `

                            var bulan = ``;
                            if(v.investasi_nilai_wajar.length){
                                $.each(v.investasi_nilai_wajar, function( keyBulan, valueBulan ){
                                    bulan += ` <td class="text-center">${formatToRupiah(valueBulan['investasi_bulan_nilai_wajar'])}</td>`
                                })
                            } else{
                                for(var i =1;i<=12;i++){
                                    bulan += ` <td class="text-center">0</td>`
                                }
                            }

                            $('#table_body').append(`<tr><td class="p-1" style="width:200px;border-right:none"><b>${v.investasi_jenis}</b></td>${html_action}${bulan}</tr>`)

                            $.each( v.investasi_subdata, function( ksubdata, vsubdata ){
                                var bulan_subdata = ``;
                                if(vsubdata.investasi_subdata_nilai_wajar.length){
                                    $.each(vsubdata.investasi_subdata_nilai_wajar, function( keyBulan, valueBulan ){
                                        bulan_subdata += ` <td class="text-center">${formatToRupiah(valueBulan['investasi_subdata_bulan_nilai_wajar'])}</td>`
                                    })
                                } else{
                                    for(var i =1;i<=12;i++){
                                        bulan_subdata += ` <td class="text-center">0</td>`
                                    }
                                }

                                let buttonEdit = (authEdit? `<a href="javascript:onEditSubdata('${vsubdata.investasi_subdata_id}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"><span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/edit-icon.svg')) !!}</span></a>`:``)
                                                    
                                let html_action = `<td class="text-center"><a href="javascript:onDetailSubdata('${vsubdata.investasi_subdata_id}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"><span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/detail-icon.svg')) !!}</span></a>${buttonEdit}</td>`

                                $('#table_body').append(`<tr><td class="" style="width:200px;border-right:none;padding-left:20px;"> ${vsubdata.investasi_subdata_nama}</td>${html_action}${bulan_subdata}</tr>`)
                            })
                        });
                    } else{
                        $('#table_body').html(`
                            <tr>
                                <td class="text-center" colspan="14">No Data Available</td>
                            </tr>
                        `);               
                    }
                } 
            }
        })
    }
    
    onAdd = () => {
        $('.form_data').show()
        $('.table_data').hide()
        $('[name=investasi_id]').val('')
        onClear();
    };

    onBack = () => {
        $('.table_data').show()
        $('.form_data').hide()
        onClear();
    };

    onRefresh = ()=>{
        onInitTable();
        onBack()
    }

    onClear = ()=>{
        $('#formData')[0].reset();
        $('#list_investasi').html('');
        $('#list_subdata').html('');
    }

    onDetail =(id)=>{
        $.ajax({
            url: urlpath.show.replace(':id', id),
            type: 'GET',
            success: function(response){
                if(response.status == true){
                    $.each(response.data, function( key, value ){
                        if(key != 'investasi_dokumen' && key != 'investasi_nilai_perolehan'){
                            $('#detail_'+key).html(value)
                        } else if (key == 'investasi_nilai_perolehan'){
                            $('#detail_'+key).html(formatToRupiah(value))
                        }
                    })

                    var firstloop = true
                    $.each(response.data.investasi_dokumen, function( key, value ){
                        if(firstloop){
                            $('#detail_dokumen_list').html('')
                            firstloop = false
                        }

                        if(value.investasi_dokumen_file != ''){
                            htmlpreview = `<a href="#" onclick="onPreviewList('${value.investasi_dokumen_file}')"class="btn btn-icon btn-info btn-active-color-light btn-sm"><span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/preview-icon.svg')) !!}</span></a>`
                        }

                        onAddList(value.investasi_dokumen_id, value.investasi_dokumen_judul, value.investasi_dokumen_file)
                        $('#detail_dokumen_list').append(`
                            <tr>
                                <td class="p-2">${value.investasi_dokumen_judul}</td>
                                <td class="text-center">${htmlpreview}</td>
                            </tr>
                        `)
                    })
                    $('#detail_modal').modal('show')

                } 
            }
        })
        
    }
    
    onEdit = (id) => {
        onAdd()
        $.ajax({
            url: urlpath.show.replace(':id', id),
            type: 'GET',
            data: {
                year: $('#investasi_tahun_table').val()
            },
            success: function(response){
                if(response.status == true){
                    $.each(response.data, function( key, value ){
                        if(key != 'investasi_dokumen'){
                            $('[name='+key+']').val(value)
                        }
                    })

                    $.each(response.data.investasi_dokumen, function( key, value ){
                        onAddList(value.investasi_dokumen_id, value.investasi_dokumen_judul, value.investasi_dokumen_file)
                    })
                    
                    $('#investasi_tahun').val($('#investasi_tahun_table').val())

                    var listbulan = response.data.investasi_nilai_wajar
                    $('.investasi_bulan_list').val('')
                    if(listbulan.length){
                        $('input[name="investasi_bulan_list[]"]').each(function(index) {
                            $(this).val(listbulan[index]['investasi_bulan_nilai_wajar']);
                        });
                    }

                    $.each(response.data.investasi_subdata, function( key, value ){
                        onAddListSubdata(value.investasi_subdata_nama,value.investasi_subdata_id)
                    })
                } 
            }
        })
    };

    onSave = () =>{
        swal({
            title: "Peringatan",
            text: "Apakah Anda Yakin Untuk Menyimpan Data?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((response) => {
            if (response) {
                const formElement = $('#formData')[0];
                var form = new FormData(formElement);
                var urlsave = ($('[name=investasi_id]').val() == '')? urlpath.insert:urlpath.update.replace(':id', $('[name=investasi_id]').val()) ;

                $.ajax({
                    url: urlsave,
                    type: 'POST',
                    data: form,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response.status == true){
                            swal("Success !", response.message, "success");
                            onRefresh()
                        } else{
                            swal("Warning", response.message, "warning");
                        }
                    }
                })
            }
        }); 
    }

    
    onDelete = (id) =>{
        swal({
            title: "Peringatan",
            text: "Apakah Anda Yakin Untuk Menghapus Data?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((response) => {
            if (response) {
                $.ajax({
                    url: urlpath.delete.replace(':id', id),
                    type: 'DELETE',
                    success: function(response){
                        if(response.status == true){
                            swal("Success !", response.message, "success");
                            onRefresh()
                        } else{
                            swal("Warning", response.message, "warning");
                        }
                    }
                })
            }
        }); 
    }
    

    function formatToRupiah(number) {
        let formattedAmount = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(number);
        return formattedAmount.replace(/,00$/, '');
    }

    onAddListSubdata =(value="",key="")=>{
        $('#list_subdata').append(`
            <div class="row">
                <div class="col-11">
                    <input class="form-control  form-control-solid w-100 mb-1" type="hidden" name='investasi_subdata_id[]' value="${key}">
                    <input class="form-control  form-control-solid w-100 mb-1" type="text" name='investasi_subdata_nama[]' placeholder="Isikan Subdata Investasi" value="${value}" required>
                </div>
                <div class="col-1 p-1">
                    <a href="#" onclick="onDeleteList(this)"class="btn btn-icon btn-danger btn-active-color-light btn-sm">
						<span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/delete-icon.svg')) !!}</span>
					</a>
                </div>
            </div> 
        `)
    }


    onAddList =(id="",nama="",file="")=>{
        var htmlpreview = ``

        if(file != ''){
            htmlpreview = `<a href="#" onclick="onPreviewList('${file}')"class="btn btn-icon btn-info btn-active-color-light btn-sm">
                        <span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/preview-icon.svg')) !!}</span>
					</a>`
        }

        $('#list_investasi').append(`
            <div class="row">
                <div class="col-5">
                    <input type="hidden" class="form-control" name="list_dokumen_id[]" placeholder="" value="${id}"/>
                    <input class="form-control  form-control-solid w-100 mb-1" type="text" name='list_dokumen_nama[]' placeholder="Isikan Nama Dokumen" value="${nama}" required>
                </div>
                <div class="col-5">
                    <input type="file" class="form-control" name="list_file_dokumen[]" placeholder="" value=""/>
                </div>
                <div class="col-2">
                    <a href="#" onclick="onDeleteList(this)"class="btn btn-icon btn-danger btn-active-color-light btn-sm">
						<span class="svg-icon svg-icon-3">{!! file_get_contents(public_path('svg/delete-icon.svg')) !!}</span>
					</a>
                    ${htmlpreview}
                </div>
            </div> 
        `)
    }

    onDeleteList = (element) =>{
        event.preventDefault()
        $(element).closest(".row").remove();
    }

    onPreviewList = (file) =>{
        $('.preview_file').html(`<embed src="{{asset('storage/investasi_dokumen/${file}') }}" style="width:100%" height="500px">`)
        $('#detail_list_preview').modal('show')
    }

    onRegenerateMonth = () =>{
        let id = $('[name=investasi_id]').val()
        $.ajax({
            url: urlpath.show.replace(':id', id),
            type: 'GET',
            data: {
                year: $('#investasi_tahun').val()
            },
            success: function(response){
                if(response.status == true){
                    var listbulan = response.data.investasi_nilai_wajar
                    $('.investasi_bulan_list').val('')

                    if(listbulan.length){
                        $('input[name="investasi_bulan_list[]"]').each(function(index) {
                            $(this).val(listbulan[index]['investasi_bulan_nilai_wajar']);
                        });
                    }
                } 
            }
        })
    }

    onEditSubdata = (id) =>{
        let year = $('#investasi_tahun_table').val()
        $('#investasi_subdata_tahun').val(year)
        $.ajax({
            url: urlpath.showSubdata.replace(':id', id),
            type: 'GET',
            data: {
                year: year
            },
            success: function(response){
                if(response.status == true){
                    $.each(response.data, function( key, value ){
                        if(key != 'investasi_subdata_nilai_wajar'){
                            $('[name='+key+']').val(value)
                        }
                    })

                    var listbulan = response.data.investasi_subdata_nilai_wajar
                    $('.investasi_subdata_bulan_list').val('')

                    if(listbulan.length){
                        $('input[name="investasi_subdata_bulan_list[]"]').each(function(index) {
                            $(this).val(listbulan[index]['investasi_subdata_bulan_nilai_wajar']);
                        });
                    }
                } 
            }
        })
        $('#subdata_update_modal').modal('show')
    }

    onSaveSubdata = () =>{
        swal({
            title: "Peringatan",
            text: "Apakah Anda Yakin Untuk Menyimpan Data?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((response) => {
            if (response) {
                const formElement = $('#formDataSubdata')[0];
                var form = new FormData(formElement);

                $.ajax({
                    url: urlpath.updateSubdata.replace(':id', $('[name=investasi_subdata_id]').val()),
                    type: 'POST',
                    data: form,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response.status == true){
                            swal("Success !", response.message, "success");
                            $('#subdata_update_modal').modal('hide')
                            onRefresh()
                        } else{
                            swal("Warning", response.message, "warning");
                        }
                    }
                })
            }
        }); 
    }

    onDetailSubdata =(id)=>{
        $.ajax({
            url: urlpath.showSubdata.replace(':id', id),
            type: 'GET',
            success: function(response){
                if(response.status == true){
                    $.each(response.data, function( key, value ){
                        if(key != 'investasi_subdata_nilai_wajar'){
                            $('#detail_'+key).html(value)
                        }
                    })
                    $('#detail_modal_subdata').modal('show')
                } 
            }
        })
        
    }

    onRegenerateMonthSub = () =>{
        let id = $('[name=investasi_subdata_id]').val()
        $.ajax({
            url: urlpath.showSubdata.replace(':id', id),
            type: 'GET',
            data: {
                year: $('#investasi_subdata_tahun').val()
            },
            success: function(response){
                if(response.status == true){
                    var listbulan = response.data.investasi_subdata_nilai_wajar
                    $('.investasi_subdata_bulan_list').val('')

                    if(listbulan.length){
                        $('input[name="investasi_subdata_bulan_list[]"]').each(function(index) {
                            $(this).val(listbulan[index]['investasi_subdata_bulan_nilai_wajar']);
                        });
                    }
                } 
            }
        })
    }
</script>