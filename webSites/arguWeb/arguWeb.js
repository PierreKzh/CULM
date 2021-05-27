//fonction de verification du formulaire
function validation(){
    //creation d'un compteur de fautes
    cpt = 0
    //on verifie la date
    dateVerif = dateV()

    codeV()

    //si le champ prenom a plus de un caractère, c'est bon
    if(document.getElementById("prenom").value.length > 0){
        cpt++
    }

    //si le champ nom a plus de un caractère, c'est bon
    if(document.getElementById("nom").value.length > 0){
        cpt++
    }

    //si la date de naissance est correct, c'est bon
    if(dateVerif == 1){
        cpt++
    }
    //on vérifie que le code vaut bien 4 caractères
    if(document.getElementById("code").value.length == 4){
        cpt++
    }

    //on vérifie que la justification soit remplie
    if(document.getElementById("jutification").value.length > 0){
        cpt++
    }

    //on compte les erreurs, si tout est bon on active le bouton valider
    if(cpt == 5){
        //le boutou n'est plus bloqué
        document.getElementById("valider").disabled = ""
        //sa couleur change
        document.getElementById("valider").style.background = "#24c1bd"
    }
    //sinon le bouton se bloque
    else{
        //le bouton est bloqué
        document.getElementById("valider").disabled = "disabled"
        document.getElementById("valider").style.background = "#24c1bc65"
    }
}

//fonction qui récupère la date actuelle
function dateActuelle(){
    //on recupère la date actuelle
    datetime = new Date()
    jour = datetime.getDate()
    mois = datetime.getMonth()+1
    an = datetime.getFullYear()

    //on réecrie la date actuelle que l'on vient de récupérer pour adapter sa forme à celle du champ input
    if(mois < 10){
        mois = "0"+mois
    }
    if(jour < 10){
        jour = "0"+jour
    }
    
    return an+"-"+mois+"-"+jour
}

//fonction pour verifier la date de naissance
function dateV(){

    //si le champ est vide ou la date est celle d'aujourd'hui ou superieure on retourne une faute
    if(document.getElementById("naissance").value == "" || document.getElementById("naissance").value >= dateActuelle()){
        return 0
    }
    //sinon c'est bon
    else{
        return 1
    }
}

//fonction pour vérifier la bonne écriture du code
function codeV(){
    //creation d'un tableau avec les caractères autorisé
    autorise = ['A','B','C','D','E','F','0','1','2','3','4','5','6','7','8','9']
    //on récupère l'emplacment du dernier caractère
    dernier = document.getElementById("code").value.length - 1
    cptCode = 0

    //tant que le dernier caractère est différent du caractère ciblé dans le tableau
    while (document.getElementById("code").value.charAt(dernier) != autorise[cptCode]){
        //on incrémente le compteur
        cptCode++
        //si tout les caractères on été différent
        if(cptCode == 16){
            //on réécrie le code avec le dernier en moins
            newCode = document.getElementById("code").value.substring(0, dernier)
            //puis on remet le code sans le dernier caractère
            document.getElementById("code").value = newCode
            break
        }
    }
}

//fonction qui change le message principale de la page arguWeb et choisi la couleur
function changementMsg(text, sousText, couleur){
    //on récupère le "text" puis on change la couleur et la police
    document.getElementById("msg").style.color = couleur
    document.getElementById("msg").style.font = 'Arial'
    //et on affiche le text
    document.getElementById("msg").innerHTML = text

    //on récupère le "text" puis on change la couleur et la police
    document.getElementById("sousMsg").style.font = 'Arial'
    //et on affiche le text
    document.getElementById("sousMsg").innerHTML = sousText
}