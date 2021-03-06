<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 xoops.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
//  @package form.php
//  @author Dirk Herrmann <alfred@simple-xoops.de>
//  @version $Id: form.php 91 2014-04-19 20:09:50Z alfred $

$ret = XoopsRequest::getInt('ret', 0, "GET");

if (isset($_POST) && count($_POST) > 0) setPost($content, $_POST);

$tueber = ($id > 0) ? constant('_AM_' . $lang_name . '_ADMENU1') : constant('_AM_' . $lang_name . '_ADDCONTENT');
$form = new XoopsThemeForm($tueber, $xoopsModule->getVar('dirname') . "_form", $_SERVER['PHP_SELF'], 'post', true);
$form->setExtra('enctype="multipart/form-data"');
if (isset($errors)) {
	$form->addElement(new XoopsFormLabel('', $errors));
}
$show_info_perm = $_SESSION['perm_' . $lang_name];

$form->addElement(new XoopsFormHidden('op', $op)); 
$form->addElement(new XoopsFormHidden('ret', $ret)); 
$form->addElement(new XoopsFormHidden('id', $content->getVar('info_id'))); 
$form->addElement(new XoopsFormHidden('frontpage', $content->getVar('frontpage')));

if ($id == 0) 
{
    $cat = XoopsRequest::getInt('cat', 0, "GET");
    $content->setVar('cat', $cat);
}

if ((in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_CAT'), $show_info_perm) && $id == 0) || (in_array(constant('_CON_' . $lang_name . '_CANUPDATE_CAT'), $show_info_perm) && $id > 0)) 
{
    $block_select = new XoopsFormSelect(constant('_AM_' . $lang_name . '_HOMEPAGE'), "cat", $content->getVar('cat'));
	$catlist = $cat_handler->getObjects(null, true, false);
	$cate = array();
	foreach ($catlist as $cats => $catr) {
		$cate[$catr['cat_id']] = $catr['title'];
	}
	$block_select->addOptionArray($cate);
	$block_select->setextra('onchange="document.forms.' . $xoopsModule->getVar('dirname') . "_form" . '.submit()"');
	$form->addElement($block_select, true);
} else {
	$form->addElement(new XoopsFormHidden("cat", $content->getVar('cat')));  
}

if ((in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_SITEART'), $show_info_perm) && $id == 0) || (in_array(constant('_CON_' . $lang_name . '_CANUPDATE_SITEART'), $show_info_perm) && $id > 0)) {
	$url_art = new XoopsFormSelect(constant('_AM_' . $lang_name . '_URLART'), "link", $content->getVar('link'));
	$url_art->addOption(0, constant('_AM_' . $lang_name . '_URL_NORMAL'));
	$url_art->addOption(6, constant('_AM_' . $lang_name . '_URL_PHP'));
	$url_art->addOption(3, constant('_AM_' . $lang_name . '_URL_KATEGORIE'));
	$url_art->addOption(2, constant('_AM_' . $lang_name . '_URL_EXTLINK'));
	$url_art->addOption(1, constant('_AM_' . $lang_name . '_URL_INTLINK'));
	$url_art->addOption(5, constant('_AM_' . $lang_name . '_URL_IFRAME'));
	$url_art->addOption(4, constant('_AM_' . $lang_name . '_URL_INTDATEI'));	
	$url_art->setExtra('onchange="document.forms.' . $xoopsModule->getVar('dirname') . "_form" . '.submit()"');
	$form->addElement($url_art, true);
} else {
	$form->addElement(new XoopsFormHidden("link", $content->getVar('link')));
} 

$form->addElement(new XoopsFormText(constant('_AM_' . $lang_name . '_LINKNAME'), "title", 80, 255, $content->getVar('title')), true); 
$form->addElement(new XoopsFormText(constant('_MI_' . $lang_name . '_TOOLTIP'), "ttip", 80, 255, $content->getVar('ttip')), false);

if (in_array($content->getVar('link'), array(0, 1, 2, 4, 5))) {
	$title_sicht = new XoopsFormCheckBox(constant('_AM_' . $lang_name . '_TITLESICHT'), 'title_sicht', $content->getVar('title_sicht'));
	$title_sicht->addOption(1, _YES);
	$form->addElement($title_sicht);
	$footer_sicht = new XoopsFormCheckBox(constant('_AM_' . $lang_name . '_FOOTERSICHT'), 'footer_sicht', $content->getVar('footer_sicht'));
	$footer_sicht->addOption(1, _YES);
	$form->addElement($footer_sicht);
} else {
	$form->addElement(new XoopsFormHidden('title_sicht', $content->getVar('title_sicht')));
	$form->addElement(new XoopsFormHidden('footer_sicht', $content->getVar('footer_sicht')));
}

$cat = ($cat == 0) ? $content->getVar('cat') : $cat;
if ( $id > 0 ) {
	$menu = $info_tree->makeMySelArray("title", "blockid",$content->getVar('parent_id'), 1, " AND cat=".$cat." AND info_id<>".$id);
} else {
	$menu = $info_tree->makeMySelArray("title", "blockid",$content->getVar('parent_id'), 1, " AND cat=".$cat);
}

if ( (in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_POSITION'),$show_info_perm) && $id == 0) || (in_array(constant('_CON_' . $lang_name . '_CANUPDATE_POSITION'),$show_info_perm) && $id > 0) ) { 
	$categoria_select = new XoopsFormSelect(constant('_AM_'.$lang_name.'_POSITION'), "parent_id",$content->getVar('parent_id'));
	$categoria_select->addOptionArray($menu);
	unset($menu);
	$form->addElement($categoria_select,true);
} else {
	$form->addElement(new XoopsFormHidden("parent_id", $content->getVar('parent_id'))); 
}  

$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_LINKID'), "blockid", 5, 5,$content->getVar('blockid')),false); 

$iframe = $content->getVar('frame');
if (in_array($content->getVar('link'),array(1,2,4,5))) {   
	switch ($content->getVar('link')) {
		case 2:
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_EXTERN'), "address", 80, 255,$content->getVar('address')),true); 
			$form->addElement(new XoopsFormHidden("height", $iframe['height'])); 
			$form->addElement(new XoopsFormHidden("border", $iframe['border']));
			$form->addElement(new XoopsFormHidden("width", $iframe['width']));
			$form->addElement(new XoopsFormHidden("align", $iframe['align']));  
			break;
		case 1:
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_INTERN'), "address", 80, 255,$content->getVar('address')),true); 
			$form->addElement(new XoopsFormHidden("height", $iframe['height'])); 
			$form->addElement(new XoopsFormHidden("border", $iframe['border']));
			$form->addElement(new XoopsFormHidden("width", $iframe['width']));
			$form->addElement(new XoopsFormHidden("align", $iframe['align'])); 
			break;
		case 4:
			$maxsizefile = intval ( constant('_CON_'.$lang_name.'_UPLADMAXSIZE') * 1024 );
			$_options = new XoopsFormElementTray(constant('_AM_'.$lang_name.'_URL'),'<br />');	
			if (in_array(constant('_CON_' . $lang_name . '_ALLCANUPLOAD'),$show_info_perm)) {
				$_options->addElement(new XoopsFormFile(constant('_AM_'.$lang_name.'_URL_DATEI_UPLOAD'), "address", $content->getVar('address'), $maxsizefile),false);
			}
			$_options->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_DATEI'), "address", 80, 255,$content->getVar('address')),false); 
			$form->addElement($_options, true);
			$form->addElement(new XoopsFormHidden('oldaddress', $content->getVar('address')));			
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_FRAME_HEIGHT'), "height", 5, 5,$iframe['height']),true); 
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_FRAME_WIDTH'), "width", 5, 5,$iframe['width']),false);
			$form->addElement(new XoopsFormHidden("border", $iframe['border']));
			$form->addElement(new XoopsFormHidden("align", $iframe['align']));
			break;
		case 5:
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_FRAME'), "address", 80, 255,$content->getVar('address')),true); 
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_FRAME_HEIGHT'), "height", 5, 5,$iframe['height']),true); 
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_FRAME_BORDER'), "border", 5, 5,$iframe['border']),true);
			$form->addElement(new XoopsFormText(constant('_AM_'.$lang_name.'_URL_FRAME_WIDTH'), "width", 5, 5,$iframe['width']),false);
			$frame_align=new XoopsFormSelect(constant('_AM_'.$lang_name.'_URL_FRAME_ALIGN'), "align", $iframe['align']);
			$frame_align->addOption('left', _LEFT);
			$frame_align->addOption('center', _CENTER);
			$frame_align->addOption('right', _RIGHT);
			$form->addElement($frame_align,true);
			break;
		default:
			$form->addElement(new XoopsFormHidden('address', $content->getVar('address')));
			$form->addElement(new XoopsFormHidden("height", $iframe['height'])); 
			$form->addElement(new XoopsFormHidden("border", $iframe['border']));
			$form->addElement(new XoopsFormHidden("width", $iframe['width']));
			$form->addElement(new XoopsFormHidden("align", $iframe['align']));
			break;
	} 
} else {
	$form->addElement(new XoopsFormHidden('address', $content->getVar('address')));    
	$form->addElement(new XoopsFormHidden("height", $iframe['height'])); 
	$form->addElement(new XoopsFormHidden("border", $iframe['border']));
	$form->addElement(new XoopsFormHidden("width", $iframe['width']));
	$form->addElement(new XoopsFormHidden("align", $iframe['align']));	
}

if ($content->getVar('link') == 1 || $content->getVar('link') == 2) {
	$menu_selfbox = new XoopsFormCheckBox(constant('_AM_'.$lang_name.'_SELF'), 'self',$content->getVar('self'));
	$menu_selfbox->addOption(1, _YES);
	$form->addElement($menu_selfbox);
} else {
	$form->addElement(new XoopsFormHidden('self', $content->getVar('self')));
}
if (intval($content->getVar('link')) == 3) {
	$menu_clickbox = new XoopsFormCheckBox(constant('_AM_'.$lang_name.'_CLICK'), 'click',$content->getVar('click'));
	$menu_clickbox->addOption(1, _YES);
	$form->addElement($menu_clickbox);
} else {
	$form->addElement(new XoopsFormHidden('click', $content->getVar('click'))); 
}

$visible_checkbox = new XoopsFormCheckBox(constant('_AM_'.$lang_name.'_VISIBLE'), 'visible', $content->getVar('visible'));
$visible_checkbox->addOption(1, _YES);
$form->addElement($visible_checkbox);

if (intval($content->getVar('link')) == 3) {
	$form->addElement(new XoopsFormHidden('submenu', $content->getVar('submenu'))); 
} else {
	$menu_checkbox = new XoopsFormCheckBox(constant('_AM_'.$lang_name.'_SUBMENU'), 'submenu',$content->getVar('submenu'));
	$menu_checkbox->addOption(1, _YES);
	$form->addElement($menu_checkbox);
}
$sgroup = explode(",",$content->getVar('visible_group')); 
if ( (in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_GROUPS'),$show_info_perm) && $id == 0) || (in_array(constant('_CON_' . $lang_name . '_CANUPDATE_GROUPS'),$show_info_perm) && $id > 0) ) {
	$groups = new XoopsFormSelectGroup(constant('_AM_'.$lang_name.'_VISIBLE_GROUP'), 'visible_group', true, $sgroup, 5,true);
	$form->addElement($groups,true);
} else {	
	foreach ($sgroup as $sg) {
		$form->addElement(new XoopsFormHidden("visible_group[]", $sg));
	}
}	

$form->addElement(new XoopsFormRadioYN(constant('_AM_'.$lang_name.'_VISIBLE_LEFTBLOCK') , 'bl_left' ,$content->getVar('bl_left') , $yes=_YES, $no=_NO));
$form->addElement(new XoopsFormRadioYN(constant('_AM_'.$lang_name.'_VISIBLE_RIGHTBLOCK'), 'bl_right',$content->getVar('bl_right'), $yes=_YES, $no=_NO));

if ($content->getVar('link') == 0 || $content->getVar('link') == 6) {
	if ($content->getVar('link') == 0) {
		$editor = XoopsRequest::getString('editor','');
		if ($editor == "") {
			$editor = xoops_getModuleOption('general_editor', 'system' );
		}
		$editor = (!empty($editor)) ? $editor : "dhtmltextarea"; 
      
		if ( !in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_HTML'),$show_info_perm ) && $content->getVar('link') == 6 ) {
			$editor = "dhtmltextarea";
			$nohtml = 1;
		} else {
			$nohtml = 0;
		}
	} else {
		$editor = "text";
		$nohtml = 1;
	}
    
	$rows = intval( $xoopsModuleConfig[$xoopsModule->getVar('dirname').'_rows'] );
	if ( $rows < 10 ) $rows = 10;
	$cols = intval( $xoopsModuleConfig[$xoopsModule->getVar('dirname').'_cols'] );
	if ( $cols < 10 ) $cols = 10;
	$width = intval( $xoopsModuleConfig[$xoopsModule->getVar('dirname').'_width'] );
	if ( $width < 10 || $width > 100) $width = 100;
	$width .= "%";
	$height = intval( $xoopsModuleConfig[$xoopsModule->getVar('dirname').'_height'] );
	if ( $height < 100 ) $height = 100;
	$height .= "px";
  
	$editor_configs = array(  	'name'   => 'content',
								'value'  => $content->getVar('content'),
								'rows'   => $rows,
								'cols'   => $cols,
								'width'  => $width,
								'height' => $height
							);   
        
	if ($xoopsModuleConfig[$xoopsModule->getVar('dirname').'_editors']== 1 && !empty($xoopsUser)) {         
		if ( $content->getVar('link') == 0 && (in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_HTML'),$show_info_perm ) ) ) {
			$select_editor = new XoopsFormSelectEditor($form, "editor", $editor, $nohtml);
			$form->addElement($select_editor);
			$editor = $select_editor->value; 
		} 
	} 
	$edi = new XoopsFormEditor(_DESCRIPTION, $editor, $editor_configs, $nohtml);
	$form->addElement($edi,true);
	if (!is_object($module_handler)) $module_handler =& xoops_gethandler('module');
	$tagmodule = $module_handler->getByDirname('tag');
	if ( is_object($tagmodule) && $tagmodule->isactive() ) {
		include_once XOOPS_ROOT_PATH . "/modules/tag/include/formtag.php";
		$form->addElement(new XoopsFormTag("tags", 100, 255, $content->getVar('tags','n')));
	}
} else {
	$form->addElement(new XoopsFormHidden("content", $content->getVar('content')));
}

if (intval($content->getVar('link')) == 0 || intval($content->getVar('link')) == 4 || intval($content->getVar('link')) == 6) {
	$option_tray = new XoopsFormElementTray(_OPTIONS,'<br />');     
	if ( (in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_HTML'),$show_info_perm) ) ) {   
	$html_checkbox = new XoopsFormCheckBox('', 'nohtml', $content->getVar('nohtml')); 
	$html_checkbox->addOption(1, _DISABLEHTML);
	$option_tray->addElement($html_checkbox);
	} else {
		$form->addElement(new XoopsFormHidden("nohtml", 1));
	}
  
	$smiley_checkbox = new XoopsFormCheckBox('', 'nosmiley', $content->getVar('nosmiley'));
	$smiley_checkbox->addOption(1, _DISABLESMILEY);
	$option_tray->addElement($smiley_checkbox);
	if ($xoopsModuleConfig['com_rule'] && $xoopsModuleConfig['com_rule'] > 0) {
		$comments_checkbox = new XoopsFormCheckBox('', 'nocomments', $content->getVar('nocomments'));
		$comments_checkbox->addOption(1, constant('_AM_' . $lang_name . '_DISABLECOM'));
		$option_tray->addElement($comments_checkbox);
  } else {
		$form->addElement(new XoopsFormHidden("nocomments", 1));
  }
  $form->addElement($option_tray);
} elseif (intval($content->getVar('link')) == 5) {
	$form->addElement(new XoopsFormHidden('content', $content->getVar('content')));
	$form->addElement(new XoopsFormHidden('nohtml', $content->getVar('nohtml'))); 
	$form->addElement(new XoopsFormHidden('nosmiley', $content->getVar('nosmiley'))); 
	$option_tray = new XoopsFormElementTray(_OPTIONS, '<br />');
	if ($xoopsModuleConfig['com_rule'] && $xoopsModuleConfig['com_rule'] > 0) {
		$comments_checkbox = new XoopsFormCheckBox('', 'nocomments', $content->getVar('nocomments'));
		$comments_checkbox->addOption(1, constant('_AM_' . $lang_name . '_DISABLECOM'));
		$option_tray->addElement($comments_checkbox);
	} else {
		$form->addElement(new XoopsFormHidden("nocomments", 1));
	}
	$form->addElement($option_tray);
} else {
	$form->addElement(new XoopsFormHidden('content', $content->getVar('content')));
	$form->addElement(new XoopsFormHidden('nohtml', $content->getVar('nohtml'))); 
	$form->addElement(new XoopsFormHidden('nosmiley', $content->getVar('nosmiley')));  
	$form->addElement(new XoopsFormHidden('nocomments', $content->getVar('nocomments'))); 
}

xoops_load('XoopsUserUtility');
$euser = $content->getVar('edited_user');
$eUser = XoopsUserUtility::getUnameFromId($euser, 0, false);
if ($content->getVar('owner') == -1) {
	if ($xoopsUser) $content->setVar('owner',$xoopsUser->uid());
	else $content->setVar('owner',0);
} 
$ouser = $content->getVar('owner');
$oUser = XoopsUserUtility::getUnameFromId($ouser, 0, false);

$form->addElement(new XoopsFormHidden('owner', $content->getVar('owner'))); 
$form->addElement(new XoopsFormLabel(constant('_AM_'.$lang_name.'_OWNER'), $oUser));	
$last_editor = $eUser;
$euser = (is_object($GLOBALS['xoopsUser'])) ? $GLOBALS['xoopsUser']->uid() : 0;
if ($id == 0) {
	$form->addElement(new XoopsFormLabel(constant('_AM_'.$lang_name.'_LAST_EDITED'),constant('_AM_'.$lang_name.'_NEWADDSITE')));
} else {
	$form->addElement(new XoopsFormHidden('edited_time', $content->getVar('edited_time')));  
	$form->addElement(new XoopsFormHidden('edited_user', $content->getVar('edited_user'))); 
  if ($content->getVar('edited_time') >0)
  {
	$last_time = ($content->getVar('edited_time') > 0) ? date(_DATESTRING,$content->getVar('edited_time')) : date(_DATESTRING);
	$last_time = ($content->getVar('edited_time') > 0) ? formatTimestamp($content->getVar('edited_time')) : formatTimestamp(time());
	$form->addElement(new XoopsFormLabel(constant('_AM_'.$lang_name.'_LAST_EDITED'), sprintf(constant('_AM_'.$lang_name.'_LAST_EDITEDTEXT'),$last_editor,$last_time)));
  } else {
	$form->addElement(new XoopsFormLabel(constant('_AM_'.$lang_name.'_LAST_EDITED'),constant('_AM_'.$lang_name.'_NEWADDSITE')));    
  }
}
$form->addElement(new XoopsFormHidden('owner', $ouser));

if ( (in_array(constant('_CON_' . $lang_name . '_ALLCANUPDATE_SITEFULL'),$show_info_perm) && $id == 0) || (in_array(constant('_CON_' . $lang_name . '_CANUPDATE_SITEFULL'),$show_info_perm) && $id > 0) ) {	
	$statusform = new XoopsFormRadio(constant('_MI_'.$lang_name.'_FREIGABEART'), "st",$content->getVar('st'));
	$statusform->addOption(1,constant('_MI_'.$lang_name.'_FREIGABEART_YES'));
	$statusform->addOption(2,constant('_MI_'.$lang_name.'_FREIGABEART_NO'));
	$form->addElement($statusform,true);
} else {
	$form->addElement(new XoopsFormHidden('st', 2));
}

$submit = new XoopsFormElementTray("", "");
$submit->addElement(new XoopsFormButton('', 'post', $tueber, 'submit'));
$cancelbutton = new XoopsFormButton('', 'button', _CANCEL, 'button');
$cancelbutton->setExtra("onclick=top.window.location='" . XOOPS_URL . "/modules/" . $xoopsModule->dirname() . "/admin/admin_seiten.php?cat=$cat'");
$submit->addElement($cancelbutton);
$form->addElement($submit, false);
$form->display();
?>