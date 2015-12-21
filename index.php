<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Tampoon</title>
    <meta name="description" content="Tampoon" />
    <style>
        *{ margin: 0; padding: 0;}

        html, body{
            width: 100%;
            height: 100%;
        }

        body
        {
        	
        	background: url('img/bg.jpg') no-repeat center center fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
            
        }

        @font-face
        {
            font-family: 'MarkerFelt';
            src: url("fonts/MarkerFelt.ttf") format('truetype');
        }

        @font-face
        {
            font-family: 'AvenirLTStd-Light';
            src: url("fonts/AvenirLTStd-Light.otf") format('truetype');
        }

        #info{ text-align: center; margin-left: auto; margin-right: auto; font-family: AvenirLTStd-Light; background-color: white; width: 800px; margin-top: 15px; }

    </style>
</head>
<body>

    <div id="info">
        <img src="img/visu2.png" style="border: none;" />
        <p style="font-size: 25px;">
        Le nouveau marqueur indélébile<br>qui identifie votre balle avec l'empreinte qui vous séduit,<br>
        choisie dans la gamme de nos nombreux motifs.<br>En rouge, vert, bleu ou noir.
        </p>
        <p style="font-size: 20px;">
            Pour voir la gamme des motifs et couleurs : cliquez <a href="img/collection.jpg">ici</a><br>
            Points de vente sur demande à l'adresse ci-dessous.<br>
            Logo personnalisé pour toute commande de 100 pièces ... et plus.
            <br>
            <b><?php echo $_SERVER['HTTP_HOST'] ?></b>
        </p>
        <p>
            <span style="font-family: MarkerFelt; font-weight: bold; font-size: 20px;">tampoon</span>, un produit <b>Presteege Partner</b><br>
            6 allée de la garenne - 78120 Clairefontaine-en-Yvelines - tél. +33 (0)1 34 84 54 34 - <a href="mailto:presteege@wanadoo.fr">presteege@wanadoo.fr</a>
        </p>
    </div>
</body>
</html>