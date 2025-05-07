	 <?php 
//var_dump($routeParams);

$response = $this->getDatas('getFLowDetails', [
	'botID'=>$routeParams['businessID'],
	'flowID'=>$routeParams['itemID'],
	  'csrfToken'=> $_SESSION['csrfToken'],
  'csrfTimestamp'=> $_SESSION['csrfTimestamp']
] ) ;
if (isset($response['message']) && $response['message']=='404') {
  $this->errorControl('404', site_url.'admin/b/');
}elseif (isset($response['message']) && $response['message']=='notPermission') {
  $this->errorControl('Permissions', site_url.'admin/b/', '');
}
  //print_r($response); 

$datas=$response['row'];?>

<form id="flowEdit">
	<input type="hidden" class="csrfToken" name="csrfToken" value="<?php echo $_SESSION['csrfToken']; ?>">
	<input type="hidden" class="csrfTimestamp" name="csrfTimestamp" value="<?php echo $_SESSION['csrfTimestamp']; ?>">
	<input type="hidden" class="" name="botID" value="<?php echo $routeParams['businessID']; ?>">
	<input type="hidden" class="" name="flowID" value="<?php echo $routeParams['itemID']; ?>">
	<div class="container px-4 px-2 card mb-3">
		<div class="row mt-3 ">
			<div class="mb-3 col-12 col-md-4 ">
				<div class="d-flex">
					<input class="form-control" type="text" name="flow_name"   id="flow_name" value="<?php echo $datas['name'];?>" autocomplete="off"  required placeholder="Flujo de..." pattern="^.{3,50}$" style="width: calc(100% - 30px);">
					<div>
						<i  class="gicon-help tips" data-bs-toggle="tooltip" data-bs-title="Puedes cambiarle el nombre a tu Flujo para identificarlo mejor. "></i> 
					</div>
				</div>
			</div> 
			<?php $disabled= $response['total']>1? '':'disabled' ?>
			<div class="mb-3 col-12 col-md-7  offset-md-1 d-flex justify-content-md-end">
				<div class="btn-toolbar" role="toolbar">
					<div class="btn-group mb-3 mb-md-0 ms-md-auto" role="group">
						<button id="add_flow" type="button" class="btn btn-outline">Añadir Flujo</button>
						<button id="edit_next" type="button" class="btn btn-outline <?php echo $disabled; ?>"><i class="gicon-arrowl"></i></button>
						<button id="edit_prev" type="button" class="btn btn-outline <?php echo $disabled; ?>"><i class="gicon-arrowr"></i></button>
						<button id="submit_save_flow" class="btn btn-primary  ms-auto d-block" >Guardar</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<div class="container mt-3">
	<div class="row">
 		<div class=" col-12 col-md-4 col-lg-4 col-sm-4 mb-3">
			<div class="card b-list-card">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<h5 class="mt-0 mb-0">Editar Flujo</h5>
						<div class="action">
							<a href="#" id="delFLow" ><i class="gicon-trash   d-block"> </i></a>
						</div>
					</div>
					<div class="row">
						<div class="col-12 mb-3">
							<label for="tag-input" class="form-label">Tipo de activación:</label>
							<?php $response = $this->getDatas('getFlowTypes') ;
                //var_dump($response); ?>
            	<select class="form-select form-control" id="flow_type" required="">
              <?php 
              if (isset($response['flowListType']) && is_array($response['flowListType'])) {
                foreach ($response['flowListType'] as $type) : ?>
								<?php 
								$selected = $type['type_id'] == $datas['type'] ? "selected" : "";?>
                  <option <?php echo $selected; ?> value="<?php echo $type['type_id']; ?>">
                    <?php echo $type['name']; ?>
                  </option>
              <?php
                endforeach;
              }?>
            	</select>
            </div>
          	<div class="col-12 mb-3">
							<label for="keywords" class="form-label">Añade las palabras de activación:</label>
							<input type="text" enterkeyhint="enter" class="form-control" id="keywords" placeholder="Hola, hi, hello">
							<?php $keywords = json_decode($datas['trigger_words'], true);
    						//print_r($keywords);
    						//$keywords = explode(",", $keywords);
								//$keywords = array_map('trim', $keywords) ;
    					?>
							<div class="tags mt-3">
								<?php	foreach ($keywords as $keyword) {
								echo	'<span class="tag">'.$keyword.'<a href="#" class=" ms-2 remove-tag" ><i class="gicon-close"></i></a></span>';	
									}
							?>
							</div>
						</div>
						<div class="col-12">
							<label  class="form-label">Añade las accciones:</label>
							<div class="row">
								<div class="col-6">
									<button id="add_text_msg" type="button" class="btn btn-outline w-100">Mensaje</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
 	 	<div class=" col-12 col-md-4 col-lg-4 col-sm-4 mb-3">
			<div class="card b-list-card ">
				<div class="card-body">
					<div class="">
						<h5 class="mt-0 mb-0">Respuestas:</h5>
					</div>
					<div id="wrap-messages" class="messages ">
						<?php  $messages = json_decode($datas['message_sequence'], true);
						  			$messages= is_array($messages)?$messages:[];
					 
							foreach ($messages as $message) {
								$content= $this->getDatas('json2html', ['content'=>$message['content']] ) ;?>
							<div class="message d-flex justify-content-between" id="m_<?php echo $message['id'] ?>" type="<?php echo $message['type'] ?>">
								<div>
									<?php 		echo '<p>' . $content . '</p>';  ?> 
								</div>
								<i class="gicon-drag draggable"></i>
							</div>
							<?php }?>
					</div>
				</div>
			</div>
 		</div>
		<div id="msg_edit" class=" col-12 col-md-4 col-lg-4 col-sm-4 d- none">
			<div class="card b-list-card">
				<div class="card-body">
					<div class="d-flex justify-content-between">
						<h5 class="mt-0 mb-0">Editar mensaje</h5>
						<div class="action">
							<a href="#" id="delmsg_m_<?php echo $messages[0]['id']; ?>" class="disabled "><i class="gicon-trash d-block"> </i></a>
						</div>
					</div>
					<div id="editor_wrap">
						<?php 
						if (isset($messages[0]['type'])&&$messages[0]['type']=='text') {
							echo '<textarea id="message-editor" rows="7" class="form-control" >'.$messages[0]['content'].'</textarea>';
							}
							else{
							echo'<div class="py-5">Ups.. Parece que hubo algún problema</div>';
							}?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>	