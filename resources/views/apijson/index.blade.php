@extends('layouts.app')

@section('css')

<link href="//cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css">
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Listagem de posts</div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tableList" class="table table-stripped   ">
                            <thead>
                                <th>User ID</th>
                                <th>ID</th>
                                <th>Titulo</th>
                                <th>Body</th>
                                <th>Ações</th>
                            </thead>
                            <tbody id="tbodyId">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    Formulário
                    <div id="status" class="float-right"></div>
                </div>

                <div class="card-body">
                    <form id="formApi">
                        <div class="form-group">
                            <label for="id">ID</label>
                            <input type="number" class="form-control" id="id" name="id" placeholder="ID" readonly>
                        </div>
                        <div class="form-group">
                            <label for="userId">User ID</label>
                            <input type="number" class="form-control" id="userId" name="userId" placeholder="User ID">
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Title">
                        </div>
                        <div class="form-group">
                            <label for="body">Body</label>
                            <input type="text" class="form-control" id="body" name="body" placeholder="Body">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="form-control btn btn-success" value="Cadastrar">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<script>
    var url_api = "https://jsonplaceholder.typicode.com/";

    function carregaTable(){
        $("#tbodyId").empty();
        fetch(url_api+'posts')
            .then(response => response.json())
            .then(function (json){
                json.forEach(function(post){
                $('#tableList > tbody:last-child').append(
                    '<tr>'
                        +'<td>'+post.userId+'</td>'
                        +'<td>'+post.id+'</td>'
                        +'<td>'+post.title+'</td>'
                        +'<td>'+post.body+'</td>'
                        +'<td><a href="javascript:void(edit('+post.id+'));" class="edit"><i class="fas fa-edit"></i></a> <a href="javascript:void(excluir('+post.id+'));" ><i class="fas fa-trash-alt"></i></a></td>'
                    +'</tr>');
                });
                $('#tableList').DataTable();
            })  
    }

    $( document ).ready(function() {
        carregaTable();
    });

    function edit(id){
        $('#status').append('Carregando');
        $('#formApi')[0].reset();
        fetch(url_api+'posts/'+id)
            .then(response => response.json())
            .then(function (json){
                $('#formApi #userId').val(json.userId);
                $('#formApi #id').val(json.id);
                $('#formApi #title').val(json.title);
                $('#formApi #body').val(json.body);
            });
        $('#status').html('Atualizando post '+id);
    }

    function excluir(id){
        fetch(url_api+'posts/'+id, {
            method: 'DELETE'
            })
        .then(function(json){
            alert('Post ' + id + ' deletado');
        })
    }

    $( "#formApi" ).submit(function( event ) {

        if ( $('#formApi #id').val() == '' ){
            $('#status').html('Cadastrando novo post');
            fetch(url_api+'posts', {
                method: 'POST',
                body: JSON.stringify({
                    title: $('#formApi #title').val(),
                    body: $('#formApi #body').val(),
                    userId: $('#formApi #userId').val()
                }),
                headers: {
                    "Content-type": "application/json; charset=UTF-8"
                }
            })
            .then(response => response.json())
            .then(function (json){
                $('#status').html('Post '+json.id+' adicionado');
                $('#tableList').DataTable().row.add( [
                    json.userId,
                    json.id,
                    json.title,
                    json.body,
                    '<a href="javascript:void(edit('+json.id+'));" class="edit"><i class="fas fa-edit"></i></a> <a href="#" ><i class="fas fa-trash-alt"></i></a>'
                ] ).draw( false );
            })
        }
        else {
            fetch(url_api+'posts/'+  $('#formApi #id').val(), {
                method: 'PUT',
                body: JSON.stringify({
                    id: $('#formApi #id').val(),
                    title: $('#formApi #title').val(),
                    body: $('#formApi #body').val(),
                    userId: $('#formApi #userId').val()
                }),
                headers: {
                "Content-type": "application/json; charset=UTF-8"
                }
            })
            .then(response => response.json())
            .then(function (json){
                console.log(json);
                $('#status').html('Post '+json.id+' atualizado');
            })
        }
        event.preventDefault();
    });
    
</script>
    
@endsection