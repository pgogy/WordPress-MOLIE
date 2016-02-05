<?PHP

	class MOLIEmenu{
	
		function __construct(){
			add_action("admin_menu", array($this, "menu_hide"));
		}
	
		function menu_hide(){
			
			global $submenu, $menu;
			
			foreach($menu as $index => $menu_item){
				if($menu_item[0]=="Linked Canvas Courses")
				{
					unset($menu[$index]);
				}
				if($menu_item[0]=="Linked Canvas Quiz")
				{
					unset($menu[$index]);
				}
				if($menu_item[0]=="Linked Canvas Quiz Answers")
				{
					unset($menu[$index]);
				}
				if($menu_item[0]=="Linked Canvas Discussions")
				{
					unset($menu[$index]);
				}
				
			}
			
		}
	
	}
	
	$MOLIEmenu = new MOLIEmenu();
	