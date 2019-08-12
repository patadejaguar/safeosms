node {
    // uncomment these 2 lines and edit the name 'node-4.4.7' according to what you choose in configuration
    // def nodeHome = tool name: 'node-4.4.7', type: 'jenkins.plugins.nodejs.tools.NodeJSInstallation'
    // env.PATH = "${nodeHome}/bin:${env.PATH}"

    stage 'check tools'
    sh "apache2 -v"
    sh "php -v"
    sh "mysql --version"

    stage 'checkout'
    checkout scm

    //stage 'clean'
    //sh "mvn clean"

    //stage 'backend tests'
    //sh "mvn test"

    // stage 'frontend tests'
    // sh "gulp test"

    //stage 'packaging'
    //sh "mvn package -DskipTests"

    stage 'deploying'
    
    sh 'find /var/lib/jenkins/workspace/safeosms-1 -type f -name "*~" -delete'
	sh 'find /var/lib/jenkins/workspace/safeosms-1 -type f -name "Thumbs.db" -delete'
	sh 'sudo cp -u -r /var/lib/jenkins/workspace/safeosms-1/* /var/www/htdocs/'

	sh 'echo "Updating Permissions"'
	
	sh 'sudo chmod -R 0755 /var/www/htdocs'
	sh 'sudo chown -R www-data:www-data /var/www/htdocs'
	
	stage 'sql-update'
	//sh 'sudo echo \"<?php ini_set(\'include_path\', ini_get(\'include_path\') .\':/var/www/htdocs/reports:/var/www/htdocs/libs:/var/www/htdocs/core\'); include_once (\'/var/www/htdocs/core/core.db.inc.php\');\\\$xTask = new cSystemTask();\\\$xTask->setBackupDB();?>\" >> /var/www/htdocs/install/daemon.update.php'
	sh 'sudo wget http://localhost/install/daemon.upd-cp.php?path=L3Zhci93d3cvaHRkb2Nz -O /var/www/htdocs/install/daemon.update.php'
	sh 'php /var/www/htdocs/install/daemon.update.php'

	//sh 'echo "remove original jar..."'
	//sh 'sudo rm -f /home/gpd/bin/gpdc1601_cierre.jar'

	//sh 'echo "Fixing Jar..."'

	//sh 'echo "Copy new jar..."'
	//sh 'sudo cp ./target/gpdc1601-1.0-SNAPSHOT.jar /home/gpd/bin/gpdc1601_cierre.jar'
	//sh 'sudo cp ./target/gpdc1601-jar-with-dependencies.jar /home/gpd/bin/gpdc1601_cierre.jar'

	//sh 'echo "Change owner and permissions jar..."'
	//sh 'sudo chmod 0755 /home/gpd/bin/gpdc1601_cierre.jar'

	//sh 'sudo chown gpd:gpd /home/gpd/bin/gpdc1601_cierre.jar'

    //sh 'echo "Probing Jar..."'

	//sh 'sudo bash /home/gpd/bin/cierre'


	sh 'echo "End Process..."'
}