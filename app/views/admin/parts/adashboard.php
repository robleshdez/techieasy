<?php 
$userStat = $this->getDatas('getTotalUser') ;
$botStat = $this->getDatas('getTotalBots') ;
//$totalUser= $response['rows'];
print_r($botStat);

 ?>
<div class="row">
<div class="col-12">
<div class="card info-card ">
	<div class="card-body">
		<h5 class="card-title">Usuarios</h5>
		<div class="d-flex align-items-center">
			<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				<i class="gicon-user"></i>
			</div>
			<div class="pt-0 p-3">
				<h6>Total</h6>
				<span class="text-primary pt-1 fw-bold"><?php echo $userStat['total_users']; ?></span> 
			</div>
			<div class="pt-0 p-3">
				<h6>Activos</h6>
				<span class="text-primary pt-1 fw-bold"><?php echo $userStat['active_users']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo $userStat['total_users'] == 0 ? 0 : round($userStat['active_users']/$userStat['total_users']*100,2)??0; ?>%</span>
			</div>
			<div class="pt-0 p-3">
				<h6>Verificados</h6>
				<span class="text-primary  pt-1 fw-bold"><?php echo $userStat['verify']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo $userStat['total_users'] == 0 ? 0 : round($userStat['verify']/$userStat['total_users']*100,2); ?>%</span>
			</div>
			<div class="pt-0 p-3">
				<h6>Sin Verificar</h6>
				<span class="text-primary  pt-1 fw-bold"><?php echo $userStat['unverify']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo $userStat['total_users'] == 0 ? 0 : round($userStat['unverify']/$userStat['total_users']*100,2); ?>%</span>
			</div>
			<div class="pt-0 p-3">
				<h6>Suspendidos</h6>
				<span class="text-primary  pt-1 fw-bold"><?php echo $userStat['suspended']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo round($userStat['suspended']/$userStat['total_users']*100,2); ?>%</span>
			</div>
		</div>
	</div>
</div>
</div>

<div class="col-12 pt-3">
<div class="card info-card ">
	<div class="card-body">
		<h5 class="card-title">Bots</h5>
		<div class="d-flex align-items-center">
			<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
				<i class="gicon-bots"></i>
			</div>
			<div class="pt-0 p-3">
				<h6>Total</h6>
				<span class="text-primary pt-1 fw-bold"><?php echo $botStat['total_bots']; ?></span> 
			</div>
			<div class="pt-0 p-3">
				<h6>Activos</h6>
				<span class="text-primary pt-1 fw-bold"><?php echo $botStat['total_active']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo $botStat['total_bots'] == 0 ? 0 : round($botStat['total_active']/$botStat['total_bots']*100,2); ?>%</span>
			</div>
			<div class="pt-0 p-3">
				<h6>Inactivos</h6>
				<span class="text-primary  pt-1 fw-bold"><?php echo $botStat['total_inactive']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo $botStat['total_bots'] == 0 ? 0 : round($botStat['total_inactive']/$botStat['total_bots']*100,2); ?>%</span>
			</div>
			<div class="pt-0 p-3">
				<h6>Bloqueados</h6>
				<span class="text-primary  pt-1 fw-bold"><?php echo $botStat['total_blocked']; ?></span> 
				<span class="text-muted  pt-2 ps-1"><?php echo $botStat['total_bots'] == 0 ? 0 : round($botStat['total_blocked']/$botStat['total_bots']*100,2); ?>%</span>
			</div>
			
		</div>
	</div>
</div>
</div>


</div>
 