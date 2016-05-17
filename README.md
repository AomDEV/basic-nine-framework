# NINE Framework for Beginner (PHP) #
Framework for Database Beginner (Easy to Use). This project is beta-test version. You can edit and share your idea.

# How to Install #
Download all files from github and extract file into your project directory. Like this : c:/xampp/htdocs/myproject/ And see next tutorial.

# Settings #
Include framework file<br />
`require("class.nine.php");`<br />
Setting Database Information<br />
`$db = new n_database("[user]","[pass]","[host]","[database name]");`<br />
Import files<br />
`$import = n_render::import_file();`<br />

# Functions #
1. View all Database function
 1.  Function name `view_all_db`
 2.  `$result = $db->view_all_db([display as table = false | true]);`
 3.  If set to "false" It's will return as array
 4.  `Array ( [0] => Array ( [0] => information_schema ) [1] => Array ( [0] => cdcol ) )`
2. View all Table function
 1. Function name `view_all_tbl`
 2. `$result = $db->view_all_tbl([display as table = false | true]);`
 3. If set to "false" It's will return as array
 4. `Array ( [0] => Array ( [0] => example ) [1] => Array ( [0] => sql_column ) )`
3. View all Column Function
 1. Function name `view_all_clmn`
 2. `$result = $db->view_all_clmn([display as table = false | true]);`
 3. If set to "false" It's will return as array
 4. `Array ( [0] => Array ( [0] => test_id ) [1] => Array ( [0] => test_user ) [2] => Array ( [0] => test_pass ) )`
4. View all Data in Table Function
 1. Function name `view_all_data`
 2. `$result = $db->view_all_data([display as table = false | true]);`
 3. If set to "false" It's will return as array
 4. `Array ( [0] => Array ( [test_id] => 1 [test_user] => test [test_pass] => test ) [1] => Array ( [test_id] => 2 [test_user] => demo [test_pass] => new_value123 ) )`
5. Update SQL Data
 1. Function name `sql_single_update_data`
 2. `$db->sql_single_update_data([table_name],[set_column],[new_value],[where_column],[where_value]);`
 3. Return null value
6. Check Function is disabled or enabled
 1. Function name `checkFunction`
 2. `$check = n_render::checkFunction([function name]);`
 3. Return true if function is enabled, Return false if function is disabled
7. Setup File Request (?page=home , ?page=info etc.)
 1. Function name `setup_request`
 2. `$setup_request = n_render::setup_request($_GET);`
 3. Return as file content if it found in "modules/page/[file].php"
 4. You can add file in "modules/page/" folder
8. [Bonus] iFrame
 1. Function name `iframe`
 2. `$iframe = n_render::iframe([url]);`
 3. Return as html tag "<iframe src='[url]'></iframe>"
<br />etc.

# Example #
Easy to use database connection syntax example like this.<br />
`require("class.nine.php");`
`$db = new n_database("username","password","host","database_name");`
`$init = $db->select('member')->find('user_id','=','1')->only(array('username','password'))->init();`
`//Return as array`
`$cmd = $db->select('member')->find('user_id','=','1')->only(array('username','password'))->get();`
`//Return SELECT username,password FROM member WHERE user_id=1;`
