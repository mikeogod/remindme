<?php
ob_start();

if(!isset($_SESSION["user_remindme"]))
{
	header("Location: index.php");
}
require("dbconnect.php");
try {
	$userId=$_SESSION["user_remindme"]["id"];
	$query="SELECT * FROM `todos` WHERE `userId`='{$userId}' AND `status`='Active' ORDER BY `priority`";
	$stmt=$db->query($query);
	$result=$stmt->fetchAll();
}
catch (PDOException $ex)
{
	$notification="Database error: ".$ex->getMessage();
	return;
}

$empty_table=false;

$table_head="
<table class='table'>
  <thead>
    <tr>
      <th>Title</th>
      <th>Description</th>
      <th></th>
    </tr>
  </thead>
  <tbody>";
$table_body="";

$row=0;
foreach($result as $todo)
{
	$row++;
	if($row%2==0)
	{
		$table_body.="<tr class='alt' row='".$row."' >";
	}
	else 
	{
		$table_body.="<tr row='".$row."' >";
	}
	$table_body.="<td style='text-align:center;'>".$todo["title"]."</td>";
	$table_body.="<td>".$todo["description"]."</td>";
	$table_body.="<td><button class='change btn btn-info'>change</button>  <button href='#' class='delete btn btn-danger'>delete</button>  <button href='#' class='save btn btn-primary'>save</button>";
	$table_body.="</tr>";
}

if(count($result)==0)
{
	$empty_table=true;
}

if(!$empty_table)
{
	$table_tail="
	</tbody>
	  <tfoot>
	  </tfoot>
	</table>";
}
else 
{
	$table_tail="
	</tbody>
	  <tfoot>
		<tr><td colspan='3'>You don't have any to do yet :( Try adding some by clicking the 'Add a new to-do item'</td></tr>
	  </tfoot>
	</table>";
}
?>

<script>
  var rowNum={value:0};
  $(document).ready(function(){
	$(".save").hide();
  	$("#todos-container table tbody").sortable({
  	  	axis:"y",
  	  	cursor:"move",
		cancel:"#todos-container table thead tr",
		containment:"#todos-container table",
		delay: 150,
		revert:"200",
		helper:fixHelper,
		start:setRowNumber,
		stop:sendUpdate
  	});

  	//Add
	$("#add_form_container").hide();
	$("#add_toggle").on("click", function(){
	  $("#add_form_container").slideToggle(function(){
		  if($("#add_form_container").css("display")=="none")
		  {
			  $("#add_toggle").html("Add New");
		  }
		  else
		  {
			  $("#add_toggle").html("Fold This");
		  }
	  });
	  
	});
	
  	$("#add_form").on("submit", function(e){
  	  	e.preventDefault();
		$.ajax({
  		  url: "todosManage.php",
  		  data: $(this).serialize(),
  		  type: "POST",
  		  ajax: true,
  		  dataType: "text",
  		  success: function(data, textStatus, jqXHR){
  	  		  location.reload();
  	  	  },
  	  	  error: function(jqXHR, textStatus, errorThrown){
  		    alert("fail: "+textStatus+" "+errorThrown+" "+jqXHR.responseText);
  	      }	  
  		});
  	  });

	  
	  $("#todos-container table tbody tr").each(function(){
		  $(this).find("td:last-Child").find(".change").on("click", function(e){
			  var title=$(this).parent().parent().find("td:first-Child").html();
			  var desc=$(this).parent().parent().find("td:first-Child").next().html();
			  $(this).parent().parent().find("td:first-Child").html("<input type='text' name='title' value='"+title+"' />").find("input").val(title).on("click", function(){
			    $(this).focus();
			  }).focus();
			  $(this).parent().parent().find("td:first-Child").next().html("<textarea name='description'></textarea>").find("textarea").val(desc).on("click", function(){
				$(this).focus();
			  });
			  
			  $(this).parent().find(".change, .delete").fadeOut(300, function(){
				  $(this).parent().find(".save").fadeIn(300);
			  });
		  });
		  $(this).find("td:last-Child").find(".delete").on("click", function(){
			  var currentRow=$(this).parent().parent();
			  currentRow.css('display', 'none');
			  var priority=currentRow.attr("row")-1;
			  //Delete
			  $.ajax({
			    url:"todosManage.php",
			    data:{
				  action:"delete",
				  priority:priority
				},
				type:"POST",
				ajax: true,
				dataType: "text",
				success: function(data, textStatus, jqXHR){
					var invisibleRows=new Array();
					$("#todos-container table tbody tr").each(function(i, row){
						if($(row).css("display")!=='undefined' && $(row).css("display")==="none")
						{
							invisibleRows.push(row);
						}
					});
					
					var allRows=$("#todos-container table tbody tr").not(invisibleRows);
					
					for(var i=0; i!=allRows.length; i++)
					{
					  if(i%2==0)
					  {
						  allRows[i].className="alt";
					  }
					  else
					  {
						  allRows[i].className="";
					  }
					}
				},
				error: function(jqXHR, textStatus, errorThrown){
		  		    alert("fail: "+textStatus+" "+errorThrown+" "+jqXHR.responseText);
		  	    },
		  	    complete: function(jqXHR, textStatus){
					var allRows=$("#todos-container table tbody tr");
			  	    for(var i=0; i!=allRows.length; i++)
			  	    {
				  	    if(allRows[i].getAttribute("row")>currentRow[0].getAttribute("row"))
				  	    {
				  	    	allRows[i].setAttribute("row", allRows[i].getAttribute("row")-1);
				  	    }
			  	    }
		  	    	currentRow.hide(400);
			  	}
			  });
			  
			  
		  });
		  $(this).find("td:last-Child").find(".save").on("click", function(){
			  var currentRow=$(this).parent().parent();
			  var title=currentRow.find("input").val();
			  var description=currentRow.find("textarea").val();
			  var priority=currentRow.attr("row")-1;
			  //Change
			  $.ajax({
				url:"todosManage.php",
				data:{
				  action:"change",
				  title: title,
				  description: description,
				  priority: priority
				},
				type: "POST",
				ajax: true,
				dataType: "text",
				success: function(data, textStatus, jqXHR){},
				error: function(jqXHR, textStatus, errorThrown){
		  		    alert("fail: "+textStatus+" "+errorThrown+" "+jqXHR.responseText);
		  	    },
		  	    complete: function(jqXHR, textStatus){
		  	    	currentRow.find("td:first-Child").html(title);
		  	    	currentRow.find("td:first-Child").next().html(description);

		  	    	currentRow.find(".save").fadeOut(300, function(){
		  	    		currentRow.find(".change, .delete").fadeIn(300);
			  	    });
			  	}
			  });
		  });
	  });
  });

  var fixHelper = function(e, ui) {
  	ui.children().each(function() {
	  $(this).width($(this).width());
	});
	return ui;
  };

  var setRowNumber=function(e, ui){
  	rowNum.value=ui.item.attr("row");
  };
  
  var sendUpdate=function(e, ui){
	var title=ui.item.find("td")[0].innerHTML;
	var description=ui.item.find("td")[1].innerHTML;
	var old_priority=rowNum.value-1;
	var new_priority;

	var allRows=$("#todos-container table tbody tr");
	for(var i=0; i!=allRows.length; i++)
	{
	  if(allRows[i].getAttribute("row")==rowNum.value)
	  {
		  new_priority=i;
	  }
	  allRows[i].setAttribute("row", i+1);

	  if(allRows[i].className!="alt" && allRows[i].getAttribute("row")%2==0)
	  {
		  allRows[i].className="alt";
	  }
	  else if(allRows[i].className=="alt" &&  allRows[i].getAttribute("row")%2!=0)
	  {
		  allRows[i].className="";
	  }
	}

	//Reorder
	var url="todosManage.php";
	$.ajax({
	  url: url,
	  type: "POST",
	  ajax: true,
	  dataType: "text",
	  data: {
		action: "reorder",
	    current: old_priority,
	    dest: new_priority
	  },
	  success: function(data, textStatus, jqXHR){
		 
	  },
	  error: function(jqXHR, textStatus, errorThrown){
		  alert("fail: "+textStatus+" "+errorThrown+" "+jqXHR.responseText);
	  }
	  
	});

  };
</script>

<div id="todos-container">
	<div id="add">
		
		<div class="panel panel-default" id="add_form_container">
			<div class="panel-body">
			  <form id="add_form" class="form-horizontal" role="form" action="todosManage.php" method="post">
			    <div class="form-group">
			    	<label for="title" class="col-sm-2 control-label">Title</label>
			    	<div class="col-sm-8">
			    	<input id="title" class="form-control" type="text" name="title" placeholder="Write the title here..."/>
			    	</div>
			  	</div>
			  	<div class="form-group">
			  		<label for="description" class="col-sm-2 control-label">Detail</label>
			  		<div class="col-sm-8">
			  			<textarea class="form-control" name="description" rows="5" cols="50" maxlength="1000" placeholder="Write the description here..."></textarea>
			  		</div>
			  	</div>
			  	<input type="text" name="action" value="add" hidden />
			  	<div class="form-group">
			  		
			  		<div class="col-sm-offset-2 col-sm-8">
			  			<input id="add-btn" class="btn btn-primary" name="add" type="submit"  value="Submit" />
			  		</div>
			  	</div>
			  </form>
		    </div>
		</div>
	</div>
	<div id="email_notification_container">
	  <?php if($_SESSION["user_remindme"]["email_enabled"]===true): ?>
	<!--   <p>You have enabled email notification</p> -->
	<!--   <p>You set the time to:</p> -->
	  <p><?php echo $_SESSION["email_time"] ?></p>
	<!--   <input type="button" id="disable_email" name="disable_email" value="Disable" /> -->
	  <?php else: ?>
	<!--   <p>You have not enabled email notification</p> -->
	<!--   <p>You can enable it here</p> -->
	<!--   <form action="index.php" method="post"> -->
	<!--     <input type="time" name="time" required /> -->
	<!--     <input type="button" id="enable_email" name="enable_email" value="Enable" /> -->
	<!--   </form> -->
	  <?php endif; ?>
	</div>
	<div class="table_container panel panel-default">
	<?php
		echo $table_head;
		echo $table_body;
		echo $table_tail;
	?>
	</div>
</div>



