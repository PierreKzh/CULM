<!DOCTYPE html>
<html lang="fr">
    <head>
        <!--choix de l'encodage des caractères-->
        <meta charset="utf-8">
        <!--titre de la page internet-->
        <title>arguWeb</title>
        <!--icone à coté du titre-->
        <link rel="icon" href="assets/iconeMedecin.png">
        <!--liaison avec fichier javaScript-->
        <script type="text/javascript" src="arguWeb.js"></script>
    </head>
    <!--creation d'une div pour placer du texte-->
    <body>
        <div style='margin-top: 50vh; transform: translateY(-50%);'>
            <!--une foi l'opération validé retour à la page principale-->
            <form method="POST" action="arguWeb.html">
                <center>
                    <table>
                        <tr>
                            <td>
                                <!--affichage du texte en fonction de l'opération-->
                                <h1 id="msg"></h1>
                                <h3 id="sousMsg"></h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <!--boutton pour valider-->
                                <center><input type="submit" value="Ok"></center>
                            </td>
                        </tr>
                    </table>
                </center>
            </form>
        </div>

        <!--Traitement PHP-->
        <?php
            //on vérifie si le formulaire n'est pas remplie
            if(empty($_POST['nom'])){
                //danc ce cas on reourne sur le formulaire
                header("Location: arguWeb.html");
            }
            else{
                //initialisation des variable pour la bdd
                $user = 'adminCULM';
                $userpassword = 'adminVR20';
                //$host = '10.0.200.245:3306';
                $host = '127.0.0.1:3306';

                //connection  la bdd
                $link = mysqli_connect($host, $user, $userpassword, 'CULM');
                //si erreur de connection
                if (!mysqli_connect($host, $user, $userpassword, 'CULM')) {
                    //echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
                    ?>
                    <!--affichage du message-->
                    <script>changementMsg("Une erreur est survenue", "", "black")</script>
                    <?php
                }
                else{
                    //vérification des entrées
                    if(strlen($_POST['nom']) > 50 or strlen($_POST['nom']) < 1){
                        ?>
                        <!--on affiche qu'il y a problème-->
                        <script>changementMsg("Le nom n'est pas rempli correctement", "", "red")</script>
                        <?php 
                    }
                    elseif(strlen($_POST['prenom']) > 50 or strlen($_POST['prenom']) < 1){
                        ?>
                        <!--on affiche qu'il y a problème-->
                        <script>changementMsg("Le prénom n'est pas rempli correctement", "", "red")</script>
                        <?php 
                    }
                    elseif($_POST['naissance'] >= date("Y-m-j")){
                        ?>
                        <!--on affiche qu'il y a problème-->
                        <script>changementMsg("La date n'est pas rempli correctement", "", "red")</script>
                        <?php
                    }
                    elseif(!ctype_xdigit($_POST['code']) or strlen($_POST['code']) != 4){
                        ?>
                        <!--on affiche qu'il y a problème-->
                        <script>changementMsg("Le code n'est pas rempli correctement", "", "red")</script>
                        <?php 
                    }
                    elseif(strlen($_POST['justification']) > 500 or strlen($_POST['justification']) < 1){
                        ?>
                        <!--on affiche qu'il y a problème-->
                        <script>changementMsg("La justification n'est pas rempli correctement", "", "red")</script>
                        <?php 
                    }
                    else{
                        //je mets les entrées dans des variables et en les modifiants si besoin
                        $nom = ucfirst($_POST['nom']);
                        $prenom = ucfirst($_POST['prenom']);
                        $naissance  = $_POST['naissance'];
                        $code = $_POST['code'];
                        $justification = str_replace("'", "\'", $_POST['justification']);

                        //sinon on récupère les données pour le code rentré
                        $testCode = mysqli_query($link, "select id_examen, code, fk_etudiant from T_EXAMEN where code = '".$code."';");
                        $resultCode = mysqli_fetch_array($testCode);
                        //si le code existe
                        if($resultCode['code'] == $code){
                            //on vérifie si il n'est attribué à aucun étudiant
                            if($resultCode['fk_etudiant'] == ""){
                                //dans ce cas on met les données dans la bdd
                                $testEtudiant = mysqli_query($link, "select * from T_ETUDIANT where nom = '".$nom."' and prenom = '".$prenom."' and date_naissance = '".$naissance."';");
                                $resultEtudiant = mysqli_fetch_array($testEtudiant);

                                //si l'étudiant existe déja
                                if($resultEtudiant['nom'] == $nom && $resultEtudiant['prenom'] == $prenom && $resultEtudiant['date_naissance'] == $naissance){
                                    //on vérifie le nombre d'examen qu'il a fait
                                    $recupNombreExamen = mysqli_query($link, "select count(fk_etudiant) from T_EXAMEN where fk_etudiant = '".$resultEtudiant['id_etudiant']."';");
                                    $transformNombreExamen = mysqli_fetch_array($recupNombreExamen);
                                    $nombreExamen = $transformNombreExamen['count(fk_etudiant)'];
                                    if($nombreExamen == 6){
                                        ?>
                                        <!--on affiche qu'il a effectué son nombre max d'examens-->
                                        <script>changementMsg("Tu as déja réalisé tes 6 examens", "Nombre d'examens restant : <?php echo(6 - $nombreExamen) ?>", "blue")</script>
                                        <?php
                                    }
                                }
                                else{
                                    //sinon on ajoute l'étudiant
                                    mysqli_query($link, "insert into T_ETUDIANT (nom, prenom, date_naissance) values ('".$nom."', '".$prenom."', '".$naissance."');");                                }

                                if($nombreExamen < 6){
                                    //on recupère les valeurs de l'étudiant au cas ou il aurait été créé
                                    $testEtudiant2 = mysqli_query($link, "select * from T_ETUDIANT where nom = '".$nom."' and prenom = '".$prenom."' and date_naissance = '".$naissance."';");
                                    $resultEtudiant2 = mysqli_fetch_array($testEtudiant2);

                                    //on ajoute l'id de l'étudiant à l'examen
                                    mysqli_query($link, "update T_EXAMEN set fk_etudiant = '".$resultEtudiant2['id_etudiant']."' where id_examen = '".$resultCode['id_examen']."';");

                                    //on récupère les prélèvements
                                    //on crée un prelevement unique
                                    mysqli_query($link, "insert into T_PRELEVEMENT (justification) values ('d8838b4è8N42Èb(38');");
                                    //on récupère l'id de ce prelevement
                                    $recupJustification = mysqli_query($link, "select id_prelevement from T_PRELEVEMENT where justification = 'd8838b4è8N42Èb(38'");
                                    $resultJustification = mysqli_fetch_array($recupJustification);
                                    //on met l'id du prelevement à l'endroit de l'examen
                                    mysqli_query($link, "update T_EXAMEN set fk_prelevement = '".$resultJustification['id_prelevement']."' where id_examen = '".$resultCode['id_examen']."';");
                                    //puis on réécrie la vrais justification
                                    mysqli_query($link, "update T_PRELEVEMENT set justification =  '".$justification."' where id_prelevement = '".$resultJustification['id_prelevement']."';");
                                    
                                    ?>
                                    <!--puis on affiche que le formulaire a bien été transmit-->
                                    <script>changementMsg("Formulaire bien reçu !", "Nombre d'examens restant : <?php echo(6 - ($nombreExamen + 1)) ?>", "blue")</script>
                                    <?php
                                }
            
                            }
                            else{
                                ?>
                                <!--sinon on affiche que le code n'est pas bon-->
                                <script>changementMsg("Le code d'examen n'est pas bon", "", "red")</script>
                                <?php
                            }
                        }
                        else{
                            ?>
                            <!--sinon c'est qu'il n'exite pas-->
                            <script>changementMsg("Le code d'examen n'existe pas", "", "red")</script>
                            <?php
                        }
                    } 
                }
                //fermeture de la connection à la bdd
                mysqli_close();
            }
        ?>
    </body>
</html>