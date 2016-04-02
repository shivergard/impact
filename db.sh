#/bin/sh
if [ -f database/database.sqlite ];
then
	read -r -p " ¯\(°_o)/¯ Are you sure? (rm rm storage/database.sqlite ) [y/N] " response
	case $response in
	    [yY][eE][sS]|[yY]) 
	        rm database/database.sqlite
			echo "DB DROPED ¯\(°_o)/¯"
	        ;;
	    *)
	        echo "¯\(°_o)/¯"
	        exit
	        ;;
	esac
fi

touch database/database.sqlite
chmod -fR 777 database/database.sqlite
