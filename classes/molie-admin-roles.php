<?PHP

	class MOLIEadminRoles{
	
		function __construct(){
			add_action("init", array($this, "create"));
		}
	
		function create(){
	
			$AdminCapabilities = array(
				'edit_linkedcanvascourse',
				'read_linkedcanvascourse',
				'delete_linkedcanvascourse',
				'edit_linkedcanvascourses',
				'edit_others_linkedcanvascourses',
				'publish_linkedcanvascourses',
				'read_private_linkedcanvascourses',
				'delete_linkedcanvascourses',
				'delete_private_linkedcanvascourses',
				'delete_published_linkedcanvascourses',
				'delete_others_linkedcanvascourses',
				'edit_private_linkedcanvascourses',
				'edit_published_linkedcanvascourses',
				'edit_linkedcanvascourses'
			);
			
			$AuthorCapabilities = array(
				'edit_linkedcanvascourse',
				'read_linkedcanvascourse',
				'delete_linkedcanvascourse',
				'edit_linkedcanvascourses',
				'publish_linkedcanvascourses',
				'read_private_linkedcanvascourses',
				'delete_linkedcanvascourses',
				'delete_private_linkedcanvascourses',
				'delete_published_linkedcanvascourses',
				'edit_private_linkedcanvascourses',
				'edit_published_linkedcanvascourses',
				'edit_linkedcanvascourses'
			);
			
			$get_users = get_users();
		
			foreach ( $get_users as $user )
			{
				if(in_array("administrator", $user->roles)){
					$user = new WP_User( $user->data->ID );
					foreach ( $AdminCapabilities as $capability ){
						$user->add_cap( $capability );
					}
				}
				if(in_array("editor", $user->roles)){
					$user = new WP_User( $user->data->ID );
					foreach ( $AuthorCapabilities as $capability ){
						$user->add_cap( $capability );
					}
				}
			}

		}
	
	}
	
	$MOLIEadminRoles = new MOLIEadminRoles();
	