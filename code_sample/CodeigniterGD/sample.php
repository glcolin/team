<?
try{
			//check if file is uploaded:
			if(isset($_FILES['profile']['name']) && !empty($_FILES['profile']['name'])){
				//make filename all lower case:
				$filename = strtolower($_FILES['profile']['name']);	
				//analyze image dimensions:
		    	$img_info = getimagesize($_FILES['profile']['tmp_name']);
				$img_width = $img_info[0];
				$img_height = $img_info[1];
				//make sure the image is at least 128x128:
				if($img_width < 128 || $img_height < 128){
					throw new Exception('Image size must be at least 128 pixel by 128 pixel.');
				}
				//make sure file type is valid:
				if (!in_array($_FILES['profile']['type'], array('image/jpeg','image/png','image/gif'))) {
				   throw new Exception('Invalid file type, only jpeg, jpg and png are allowed.');
				}
				//make sure file extension is valid:
				$extention = pathinfo($filename, PATHINFO_EXTENSION);
				if(!in_array($extention ,array('gif','png' ,'jpg','jpeg')) ) {
				    throw new Exception('Invalid file extension, only jpeg, jpg and png are allowed.');
				}
				//make sure file is less than 1MB:
				if ($_FILES['profile']['size'] > 3145728) {
					throw new Exception('File size exceeds 3 Mb, please upload a smaller file.');
				}
				//delete agent's previous files then upload file:
				if(is_file($file_temp_name = $this->config->item('shared_avatar_path').'agent-'.$agentId.'.jpg')){unlink($file_temp_name);}
				if(is_file($file_temp_name = $this->config->item('shared_avatar_path').'agent-'.$agentId.'.png')){unlink($file_temp_name);}
				if(is_file($file_temp_name = $this->config->item('shared_avatar_path').'agent-'.$agentId.'.jpeg')){unlink($file_temp_name);}
				if(is_file($file_temp_name = $this->config->item('shared_avatar_path').'agent-'.$agentId.'.gif')){unlink($file_temp_name);}
				$newfilename = $this->config->item('shared_avatar_path').'agent-'.$agentId.'.'.$extention;
				//upload:
			    if (move_uploaded_file($_FILES['profile']['tmp_name'],$newfilename)){
			    	//resize image file:
			    	$this->load->library('image_lib');
			    	$imgconfig = array();
			    	$imgconfig['image_library'] = 'gd2';
					$imgconfig['source_image'] = $newfilename;
					$imgconfig['create_thumb'] = FALSE;
					$imgconfig['maintain_ratio'] = TRUE;
					if($img_width >= $img_height){
						$imgconfig['height'] = 128;
						$imgconfig['width'] = 128 * $img_width / $img_height;
					}
					if($img_height >= $img_width ){
						$imgconfig['width'] = 128; 
						$imgconfig['height'] = 128 * $img_height / $img_width;
					}
					$this->image_lib->clear(); 
        			$this->image_lib->initialize($imgconfig);
					if (!$this->image_lib->resize()){
			            die($this->image_lib->display_errors());
			        }
					//set offset for cropping:
					$x_offset = round(($imgconfig['width']-128)/2);
					$y_offset = round(($imgconfig['height']-128)/2);
					//crop image file:
					$des_img = @imagecreatetruecolor(128, 128);
					switch($extention){
						case 'png':
								$src_img = @imagecreatefrompng($newfilename);
								imagecopyresampled($des_img, $src_img, 0, 0, $x_offset, $y_offset, 128, 128, 128, 128);
								imagepng($des_img,$newfilename);
								break;
						case 'jpg':
						case 'jpeg':
								$src_img = @imagecreatefromjpeg($newfilename);
								imagecopyresampled($des_img, $src_img, 0, 0, $x_offset, $y_offset, 128, 128, 128, 128);
								imagejpeg($des_img,$newfilename);
								break;
						case 'gif':
								$src_img = @imagecreatefromgif($newfilename);
								imagecopyresampled($des_img, $src_img, 0, 0, $x_offset, $y_offset, 128, 128, 128, 128);
								imagegif($des_img,$newfilename);
								break;
						deafault:
								break;
					}
					//clear up memory:
					imagedestroy($des_img);
					imagedestroy($src_img);
			    }else{
			    	throw new Exception('Failed to upload file.');
			    }
			}
		}catch(Exception $e){
			$this->session->set_userdata('error',$e->getMessage());
		}//End profile image upload;