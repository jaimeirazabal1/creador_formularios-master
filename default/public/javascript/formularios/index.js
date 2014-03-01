$(document).ready(function(){
	//constantes
	var CANT_FILAS = -1;
	// 
	//agregar una fila
	$('body').on('click','#add_row',function(e){
		e.preventDefault();
		CANT_FILAS++;
		$('body').off('click','#add_row',function(e){})
		var contenido_fila = '<td><input type="text" required name="'+CANT_FILAS+'_nombre_campo" class="form-control nombre_campo_db" placeholder="Nombre de Campo"></td>'+
							 

							 '<td><select name="'+CANT_FILAS+'_type" class="form-control tipo_dato_db">'+
							 '<option value="">Seleccione</option>'+
							 '<option value="varchar">String</option>'+
							 '<option value="int">Números</option>'+
							 '<option value="fecha">Fecha</option>'+
							 '<option value="text">Párrafo</option>'+
							 '<option value="boolean">Boleano</option>'+
							 '</select></td>'+

							 '<td><select name="'+CANT_FILAS+'_constrain_db" class="form-control constrain_db">'+
							 '<option value="normal">Normal</option>'+
							 '<option value="unique">Campo Unico</option>'+
							 '</select></td>'+

							 '<td><input type="number" name="'+CANT_FILAS+'_size" min="0" class="form-control tamano_campo_db" placeholder="Tamaño de Campo"></td>'+
							 '<td><input type="checkbox" name="'+CANT_FILAS+'_not_null" value="1" title="Click para permitir este campo nulo"></td>'+

							 '<td><textarea class=" form-control comentario_campo_db" name="'+CANT_FILAS+'_comentario_campo_db" placeholder="Descripción de Campo"></textarea></td>'+
							 '<td><spam class="glyphicon glyphicon-remove borrar_fila"></spam></td>';

		var nueva_fila = '<tr>'+contenido_fila+'</tr>';
		
		$("#table_new_db_columns").append(nueva_fila);
	})
	// borrar fila
	$("body").on('click','.borrar_fila',function(){
		$("body").off('click','.borrar_fila',function(){})
		if (confirm('Esta seguro de borrar la fila?')) {
			$(this).parent().parent().remove();
		};
	})
	//cambia puntero al colocarse encima de la x
	$('body').on('mouseenter','.borrar_fila',function(){
		$('body').off('mouseenter','.borrar_fila',function(){})
		$(this).css('cursor','pointer');
	})
	$("body").on("change",".tipo_dato_db",function(){
		$("body").off("change",".tipo_dato_db",function(){})
		if ($(this).val()=="varchar") {
			tamano_de_fila = $(this).parent().parent().find(".tamano_campo_db");
			
			if (tamano_de_fila.prop("tagName")=="SELECT") {
				tamano_de_fila.parent().html('<input type="number" name="'+CANT_FILAS+'_size" min="0" class="form-control tamano_campo_db" required="required" value="100" placeholder="Tamaño de Campo">')
			};
			tamano_de_fila.val("100").attr("required",true);
		}else if ($(this).val()=="text" || $(this).val()=="int" || $(this).val()=="boolean") {
			tamano_de_fila = $(this).parent().parent().find(".tamano_campo_db");
			if (tamano_de_fila.prop("tagName")=="SELECT") {
				tamano_de_fila.parent().html('<input type="number" name="'+CANT_FILAS+'_size" min="0" class="form-control tamano_campo_db" placeholder="Tamaño de Campo">')
			};
			tamano_de_fila.val("").attr("required",false);
		}else if ($(this).val()=="fecha") {
			tamano_de_fila = $(this).parent().parent().find(".tamano_campo_db");
			name = tamano_de_fila.attr("name");
			tamano_de_fila.parent().html('<select name="'+name+'" class="form-control tamano_campo_db">'+
										'<option value="timestamp">yyyy-mm-dd hh:mm:ss</option>'+
										'<option value="date">yyyy-mm-dd</option>'+
										'</select>');
		};

	});
	//procesar form
	$('#form_nuevo_modelo').submit(function(e){
		e.preventDefault();
		$.ajax({
			type:"POST",
			data:$(this).serialize(),
			url:"",
			success:function(r){
				alert(r.mensaje);
				if (r.php) {
					alert(r.php);
				};
				if (r.bol) {
					location.reload(1);
				};
				
			},
			error:function(e){
				alert(e);
			}
		})
	})
	$("body").on("change",".tablas",function(){
		$("body").off("change",".tablas",function(){})
		url = location.href;
		url = url.split("index");
		if (url.length == 2) {
			delete url[url.length]
		};
		console.log("url",url)
		if ($(this).val()!=0) {
			$.ajax({
				async:false,
				url:url[0]+"/describe/"+$("#tablas_tablas :selected").text(),
				success:function(r){
					$("#crear_vistas").attr("modelo",$("#tablas_tablas :selected").text())
					$('.modal').modal('show')
					console.log(r);
					var header='<thead>';
					contenido = r.contenido;
					$(".modal-title").html($("#tablas_tablas :selected").text());
					propiedades=[];
					for (var i = 0; i < r.header.length; i++) {
						header += "<th>"+r.header[i].Field+"\n Null: "+r.header[i].Null+", Type:"+r.header[i].Type+"</th>";
						propiedades.push(r.header[i].Field);
					};
					
					header +="</thead>";
					body ='';
					for (var i = 0; i<contenido.length; i++) {
						body += "<tr>"
						$.each(contenido[i], function( key, value ){
							body +="<td>"+value+"</td>";
						})
						body +="</tr>";
					};
					
					$("#tabla").html(header+body);
				}
			})
		}else{

		}
	})
	$("body").on("click","#crear_vistas",function(){
		$("body").off("click","#crear_vistas",function(){})
		modelo = $(this).attr("modelo");
		url = location.href;
		url = url.split("index");
		if (url.length == 2) {
			delete url[url.length]
		};
		$.ajax({
			async:false,
			url:url[0]+"/crear_scaffold_controller/"+$("#tablas_tablas :selected").text(),
			success:function(r){
				url = location.href;
				url = url.split("index");
				console.log(r)
				alert(r);
				if (r) {
					if (url.length == 2) {
						location.href=url+"/../../"+modelo;
					}else{
						location.href=url+"/../"+modelo;
					}
				};
				
				
			}
		})
	})
});
