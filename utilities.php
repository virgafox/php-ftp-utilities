<?php
		if(isset($_POST['unzip_element'])) {
			$sourcename = filter_var($_POST['unzip_element'], FILTER_SANITIZE_STRING);
			if (!extension_loaded('zip')) {
				$message['type'] = 'danger';
				$message['text'] = "Unzip: ERROR. The Zip php extension is not loaded on this server.";
			} else if (strlen($sourcename) > 1 && strlen($sourcename) < 40) {
				flush();
				usleep(500);
				if (unzip_exec($sourcename) === TRUE) {
					$message['type'] = 'success';
					$message['text'] = "Unzip: Zip File extracted in <strong>".basename($sourcename, ".zip")."</strong> directory!";
				} else {
					$message['type'] = 'danger';
					$message['text'] = "Unzip: An Error occurred during extraction.";
				}
				
			} else {
				$message['type'] = 'danger';
				$message['text'] = "Unzip: An Error occurred, extraction not started.";
			}
		}
		
		function unzip_exec($sourcename) {
			$zip = new ZipArchive;
			$res = $zip->open($sourcename);
			if ($res === TRUE) {
				$targetdir = "./".basename($sourcename, ".zip");
				if (is_dir($targetdir))
					delete_exec($targetdir);
				mkdir($targetdir, 0777);
				$zip->extractTo($targetdir);
				return $zip->close();
			} else {
				return false;
			}
		}
		
		if(isset($_POST['zip_element'])) {
			$sourcename = filter_var($_POST['zip_element'], FILTER_SANITIZE_STRING);
			if (!extension_loaded('zip')) {
				$message['type'] = 'danger';
				$message['text'] = "Zip: ERROR. The Zip php extension is not loaded on this server.";
			} else if (strlen($sourcename) > 1 && strlen($sourcename) < 40) {
				flush();
				usleep(500);
				if (zip_exec($sourcename, "./".$sourcename.".zip") === TRUE) {
					$message['type'] = 'success';
					$message['text'] = "Zip: Folder compressed in <strong>".$sourcename.".zip</strong> file.";
				} else {
					$message['type'] = 'danger';
					$message['text'] = "Zip: An Error occurred during compression.";
				}
				
			} else {
				$message['type'] = 'danger';
				$message['text'] = "Zip: An Error occurred, compression not started.";
			}
		}
		
		function zip_exec($source, $destination)
		{
		    if (!extension_loaded('zip') || !file_exists($source)) {
		        return false;
		    }
		
		    $zip = new ZipArchive();
		    
		    if (is_file($destination))
		    	delete_exec($destination);
		    
		    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
		        return false;
		    }
		
		    $source = str_replace('\\', '/', realpath($source));
		
		    if (is_dir($source) === true)
		    {
		        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
		
		        foreach ($files as $file)
		        {
		            $file = str_replace('\\', '/', $file);
		
		            // Ignore "." and ".." folders
		            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
		                continue;
		
		            $file = realpath($file);
		
		            if (is_dir($file) === true)
		            {
		                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
		            }
		            else if (is_file($file) === true)
		            {
		                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
		            }
		        }
		    }
		    else if (is_file($source) === true)
		    {
		        $zip->addFromString(basename($source), file_get_contents($source));
		    }
		
		    return $zip->close();
		}
		
		if(isset($_POST['delete_element'])) {
			$sourcename = filter_var($_POST['delete_element'], FILTER_SANITIZE_STRING);
		
			if (strlen($sourcename) > 1 && strlen($sourcename) < 40) {
				flush();
				usleep(500);
				if (delete_exec($sourcename) === TRUE) {
					$message['type'] = 'success';
					$message['text'] = "Delete: Element <strong>".$sourcename."</strong> deleted succesfully.";
				} else {
					$message['type'] = 'danger';
					$message['text'] = "DELETE: An Error occurred during deletion.";
				}
			} else {
				$message['type'] = 'danger';
				$message['text'] = "Delete: An Error occurred, deletion not started.";
			}
		}
		
		function delete_exec($sourcename) {
			if (is_dir($sourcename)) {
				foreach (new DirectoryIterator($sourcename) as $fileInfo) {
					if (!$fileInfo->isDot()) {
						if (is_dir($fileInfo->getPathname()))
							delete_exec($fileInfo->getPathname());
						else
							unlink($fileInfo->getPathname());
					}
				}
				rmdir($sourcename);
			} else {
				unlink($sourcename);
			}
			return true;
		}
?> 

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP FTP Utilities</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
	    <div class="page-header">
			<h1>PHP FTP Utilities</h1>
		</div>
		
		<?php
			if(isset($message)) {
				echo "<div class=\"alert alert-".$message['type']." alert-dismissable\">";
				echo "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>";
				echo $message['text'];
				echo "</div>";
			}
		?>
		
		<div class="row">
		<div class="col-md-8">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs">
		  <li class="active"><a href="#unzip" data-toggle="tab">Unzip</a></li>
		  <li><a href="#zip" data-toggle="tab">Zip</a></li>
		  <li><a href="#delete" data-toggle="tab">Delete</a></li>
		</ul>
		
		<!-- Tab panes -->
		<div class="tab-content">
		
			<!-- UNZIPPER -->
		  <div class="tab-pane fade in active" id="unzip">
		  	<h2>Unzipper</h2>
		  	<hr>
		  	<div class="row">
		  		  <div class="col-md-6">
				  		<div class="panel panel-warning">
						  <div class="panel-heading">
						    <h3 class="panel-title">Unzip Instructions</h3>
						  </div>
						  <div class="panel-body">
						    Put this script file in the same folder of the archive that you want to extract. Visit this page and select the zip archive, then press "Unzip!". The archive will be extracted in a folder with the same name.<br> <strong>Warning</strong>, if that folder already exists, it will be overwritten.
						  </div>
						</div>
				  </div><!-- col -->
				  <div class="col-md-6">
					  <form action="#" method="POST" role="form">
						<div class="form-group">
							<label for="unzip_element">Select archive</label>
							<select name="unzip_element" class="form-control">
								<option value=""> --- </option>
								<?php
								foreach (new DirectoryIterator(__DIR__) as $fileInfo) {
									if (!($fileInfo->isDot())&&$fileInfo->isFile()) {
										$path_parts = pathinfo($fileInfo);
										if (($path_parts['extension'] == 'zip') || ($path_parts['extension'] == 'ZIP'))
											echo "<option value=\"".$fileInfo."\">".$fileInfo."</option>", "\n";
									}
								}
								?>
							</select>
						</div>
						<button type="submit" class="btn btn-warning"><span class="glyphicon glyphicon-folder-open"></span> Unzip!</button>
					  </form>
				  </div> <!-- col -->
			</div><!-- row -->
		  </div>
		  
		  <!-- ZIPPER -->
		  <div class="tab-pane fade" id="zip">
		  <h2>Zipper</h2>
		  	<hr>
			  <div class="row">
		  		  <div class="col-md-6">
				  		<div class="panel panel-primary">
						  <div class="panel-heading">
						    <h3 class="panel-title">Zip Instructions</h3>
						  </div>
						  <div class="panel-body">
						    Put this script file in the same directory of the folder that you want to compress. Visit this page and select the folder, then press "Zip!". The folder will be compressed in an archive with the same name.<br><strong>Warning</strong>, if the archive with this name already exists, it will be overwritten.
						  </div>
						</div>
				  </div><!-- col -->
				  <div class="col-md-6">
					  <form action="#" method="POST" role="form">
						<div class="form-group">
							<label for="zip_element">Select folder</label>
							<select name="zip_element" class="form-control">
								<option value=""> --- </option>
								<?php
								foreach (new DirectoryIterator(__DIR__) as $fileInfo) {
									if (!($fileInfo->isDot())&&($fileInfo->isDir())) {
										echo "<option value=\"".$fileInfo."\">".$fileInfo."</option>", "\n";
									}
								}
								?>
							</select>
						</div>
						<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-compressed"></span> Zip!</button>
					  </form>
				  </div> <!-- col -->
			</div><!-- row -->
		  </div>
		  <div class="tab-pane fade" id="delete">
		  <h2>Deleter</h2>
		  	<hr>
			  <div class="row">
		  		  <div class="col-md-6">
				  		<div class="panel panel-danger">
						  <div class="panel-heading">
						    <h3 class="panel-title">Delete Instructions</h3>
						  </div>
						  <div class="panel-body">
						    Put this script file in the same folder of the element (file or folder) that you want to delete. Visit this page and select the element, then press "Delete!".<br> <strong>Warning</strong>, the element will be deleted immediately.
						  </div>
						</div>
				  </div><!-- col -->
				  <div class="col-md-6">
					  <form action="#" method="POST" role="form">
						<div class="form-group">
							<label for="delete_element">Select folder or file</label>
							<select name="delete_element" class="form-control">
								<option value=""> --- </option>
								<?php
								foreach (new DirectoryIterator(__DIR__) as $fileInfo) {
									if (!($fileInfo->isDot())&&($fileInfo != basename(__FILE__))) {
										echo "<option value=\"".$fileInfo."\">".$fileInfo."</option>", "\n";
									}
								}
								?>
							</select>
						</div>
						<button type="submit" class="btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete!</button>
					  </form>
				  </div> <!-- col -->
			</div><!-- row -->
		  </div>
		</div>
        <hr class="visible-xs visible-sm">
		</div><!-- col 8 -->
		<div class="col-md-4">
			<div class="panel panel-default">
			  <!-- Default panel contents -->
			  <div class="panel-heading">
			  	<h3 class="panel-title">Actual Directory Content</h3>
			  </div>
			  <!-- Table -->
			  <table class="table table-condensed table-hover">
			    <thead>
			    	<tr>
			    		<th>Name</th>
						<th>Type</th>
			    	</tr>
			    </thead>
			    <tbody>
			    <?php
					foreach (new DirectoryIterator(__DIR__) as $fileInfo) {
						if (!($fileInfo->isDot())&&($fileInfo != basename(__FILE__))) {
							echo "<tr>";
							if(is_dir($fileInfo)) {
								echo "<td>".$fileInfo."</td>";
								echo "<td>Folder</td>";
							} else {
								echo "<td>".$fileInfo."</td>";
								echo "<td>File</td>";
							}
							echo "</td>";
						}
					}
				?>
			    </tbody>
			  </table>
			</div>
		</div><!-- col4 -->
		</div><!-- row big -->
	  <hr>
      <footer>
        <p>Project on <a href="https://github.com/virgafox/php-ftp-utilities">GitHub</a>.</p>
      </footer>  
    </div>
    
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
  </body>
</html>