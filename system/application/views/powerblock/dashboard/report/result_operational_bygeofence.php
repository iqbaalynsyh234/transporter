<script type="text/javascript">
	jQuery(document).ready(function(){
		// EXPORT EXCEL
		jQuery("#export_xcel").click(function(){
			window.open('data:application/vnd.ms-excel,' + encodeURIComponent(jQuery('#isexport_xcel').html()));
		});
	});
</script>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="panel">
			<header class="panel-heading panel-heading-blue">Operational Report</header>
				<div class="panel-body" id="bar-parent10">
					<div class="row">
						<div class="col-lg-4 col-sm-4">
							<a href="javascript:void(0);" id="export_xcel" type="button" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect m-b-10 btn-circle btn-info"><small>Export to Excel</small></a>
						</div>
						<div id="isexport_xcel">
							<table width="100%" cellpadding="3" class="table sortable no-margin" style="margin: 3px;">
								<thead>
							    	<tr>
								        <th style="text-align:center;" width="3%">No</td>
										<th style="text-align:center;" width="10%">Vehicle</th>
										<th style="text-align:center;" width="10%">Start Time</th>
										<th style="text-align:center;" width="10%">End Time</th>
										<th style="text-align:center;" width="5%">Engine</th>
										<th style="text-align:center;" width="7%">Duration</th>
										<th style="text-align:center;" width="20%">Location Start</th>
										<th style="text-align:center;" width="20%">Location End</th>
										<th style="text-align:center;" width="7%">Trip Mileage</th>
										<th style="text-align:center;" width="7%">Commulative Mileage</th>

									</tr>
							    </thead>
								<tbody>
									<?php
										if (count($data)>0)
										{
											$j = 0;
											for ($i=0;$i<count($data);$i++)
											{
												$j = $j + $data[$i]->trip_mileage_trip_mileage;
											?>
											<tr>
												<td style="text-align:center;" ><?php echo $i+1;?></td>
												<td><?php echo $data[$i]->trip_mileage_vehicle_name." ".$data[$i]->trip_mileage_vehicle_no;?></td>
												<td><?php echo $data[$i]->trip_mileage_start_time;?></td>
												<td><?php echo $data[$i]->trip_mileage_end_time;?></td>
												<td style="text-align:center;" >
													<?php if($data[$i]->trip_mileage_engine == 0) { ?>
														<?php echo "OFF";?>
													<?php } else { ?>
														<?php echo "ON";?>
													<?php } ?>
												</td>
												<td><?php echo $data[$i]->trip_mileage_duration;?></td>
												<td>
													<?php $geofence_start = strlen($data[$i]->trip_mileage_geofence_start);
														if (strlen($geofence_start == 1)){
														$geofence_start_name = "";?>
															<strong><font color="red"><?php echo $geofence_start_name;?></font></strong><br />
															<?php echo $data[$i]->trip_mileage_location_start;?><br />
													<?php } ?>

													<?php
														if (strlen($geofence_start > 1)){
														$geofence_start_name = $data[$i]->trip_mileage_geofence_start;?>
															<strong><font color="red"><?php echo $geofence_start_name;?></font></strong><br />
															<?php echo $data[$i]->trip_mileage_location_start;?><br />
													<?php } ?>
															<a target="_blank" href="http://maps.google.com/maps?q=<?=$data[$i]->trip_mileage_coordinate_start;?>">
															<font color="blue"><?php echo $data[$i]->trip_mileage_coordinate_start;?></font></a>

												</td>

												<td>
													<?php $geofence_end = strlen($data[$i]->trip_mileage_geofence_end);
														if (strlen($geofence_end == 1)){
														$geofence_end_name = "";?>
															<strong><font color="red"><?php echo $geofence_end_name;?></font></strong><br />
															<?php echo $data[$i]->trip_mileage_location_end;?><br />
													<?php } ?>

													<?php
														if (strlen($geofence_end > 1)){
														$geofence_end_name = $data[$i]->trip_mileage_geofence_end;?>
															<strong><font color="red"><?php echo $geofence_end_name;?></font></strong><br />
															<?php echo $data[$i]->trip_mileage_location_end;?><br />
													<?php } ?>
															<a target="_blank" href="http://maps.google.com/maps?q=<?=$data[$i]->trip_mileage_coordinate_end;?>">
															<font color="blue"><?php echo $data[$i]->trip_mileage_coordinate_end;?></font></a>
												</td>

												<td><?php echo $data[$i]->trip_mileage_trip_mileage." "."KM";?></td>
												<td><?php echo $j." "."KM";?></td>
											</tr>
									<?php
											}
										}else{
											echo "<tr><td colspan='12'>No Data Available</td></tr>";
										}
										?>
							    </tbody>

									<tr>
										<td colspan="7"><strong>Total Mileage</strong></td>
										<td colspan="3" style="text-align:center;"><strong>
											<?php if (isset($j) && $j > 0){
												echo $j." "."KM";
											}else{
												echo "";
											}
											?></strong></td>
									</tr>
									<tr>
									<td colspan="7"><strong>Total Duration ON</strong></td>
									<td colspan="3" style="text-align:center;"><strong><?php
										if (isset($totalduration))
																{
																	$conval = $totalduration;
																	$seconds = $conval;

																	// extract hours
																	$hours = floor($seconds / (60 * 60));

																	// extract minutes
																	$divisor_for_minutes = $seconds % (60 * 60);
																	$minutes = floor($divisor_for_minutes / 60);

																	// extract the remaining seconds
																	$divisor_for_seconds = $divisor_for_minutes % 60;
																	$seconds = ceil($divisor_for_seconds);

																	if(isset($hours) && $hours > 0)
																	{
																		if($hours > 0 && $hours <= 1)
																		{
																			echo $hours." "."Hour"." ";
																		}
																		if($hours >= 2)
																		{
																			echo $hours." "."Hours"." ";
																		}
																	}
																	if(isset($minutes) && $minutes > 0)
																	{
																		if($minutes > 0 && $minutes <= 1 )
																		{
																			echo $minutes." "."Minute"." ";
																		}
																		if($minutes >= 2)
																		{
																			echo $minutes." "."Minutes"." ";
																		}
																	}
																	/*  if(isset($seconds) && $seconds > 0)
																	{
																		echo $seconds." "."Detik"." ";
																	} */
																}

																?></strong>

										</td>
									</tr>
							</table>
						</div>
					</div>
				</div>
		</div>
	</div>
</div>
