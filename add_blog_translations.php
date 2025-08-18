<?php
/**
 * Add French and Kinyarwanda Blog Translations
 */

require_once 'config/database.php';

try {
    echo "Adding French and Kinyarwanda blog translations...\n";
    
    // French translations
    $french_translations = [
        [
            'blog_post_id' => 1,
            'title' => 'Lancement du Nouveau Système de Demande de Certificats en Ligne',
            'excerpt' => 'Le Diocèse de Byumba a le plaisir d\'annoncer le lancement de notre nouveau système de demande de certificats en ligne, facilitant la demande de documents importants pour les paroissiens.',
            'content' => 'Le Diocèse de Byumba a le plaisir d\'annoncer le lancement de notre nouveau système de demande de certificats en ligne. Cette plateforme numérique simplifiera le processus de demande de certificats de baptême, confirmation, mariage et autres documents importants.\n\nCaractéristiques principales :\n- Soumission de demandes en ligne\n- Capacité de téléchargement de documents\n- Suivi du statut en temps réel\n- Traitement sécurisé des paiements\n- Support multilingue\n\nLes paroissiens peuvent maintenant demander des certificats depuis le confort de leur domicile et suivre le progrès de leurs demandes en ligne. Cette initiative fait partie de nos efforts continus pour moderniser nos services et mieux servir notre communauté.\n\nPour accéder au système, visitez notre site web et créez un compte. Pour assistance, veuillez contacter notre bureau pendant les heures d\'ouverture.'
        ],
        [
            'blog_post_id' => 2,
            'title' => 'Retraite Annuelle des Jeunes du Diocèse 2024',
            'excerpt' => 'Rejoignez-nous pour la Retraite Annuelle des Jeunes du Diocèse du 15 au 17 février 2024, au Lac Ruhondo. Un week-end de croissance spirituelle, de communion et d\'activités amusantes pour les jeunes catholiques.',
            'content' => 'Le Diocèse de Byumba invite tous les jeunes catholiques âgés de 16 à 30 ans à participer à notre Retraite Annuelle des Jeunes du 15 au 17 février 2024, au magnifique centre de retraite du Lac Ruhondo.\n\nLe thème de cette année est "Appelés à Servir" et comprendra :\n- Conférences inspirantes par des orateurs invités\n- Discussions en petits groupes\n- Adoration et Messe\n- Activités récréatives\n- Spectacles culturels\n- Opportunités de réseautage\n\nLa retraite vise à renforcer la foi, construire la communauté et inspirer les jeunes à prendre des rôles actifs dans leurs paroisses et communautés.\n\nFrais d\'inscription : 25 000 FRW (comprend l\'hébergement, les repas et le matériel)\nDate limite d\'inscription : 5 février 2024\n\nPour vous inscrire, contactez votre coordinateur paroissial des jeunes ou visitez notre bureau. Places limitées - inscrivez-vous tôt !'
        ],
        [
            'blog_post_id' => 3,
            'title' => 'Préparation et Activités de la Saison du Carême',
            'excerpt' => 'Alors que nous approchons de la saison sainte du Carême, le Diocèse de Byumba annonce des programmes et activités spéciaux pour aider les fidèles à se préparer pour Pâques.',
            'content' => 'La saison du Carême est un temps de prière, de jeûne et d\'aumône alors que nous préparons nos cœurs pour la célébration de Pâques. Le Diocèse de Byumba a préparé des programmes spéciaux pour accompagner les fidèles pendant cette saison sainte.\n\nActivités du Carême :\n- Chemin de Croix hebdomadaire (vendredis à 18h00)\n- Retraite de Carême pour adultes (2-3 mars)\n- Programme de Carême pour enfants\n- Horaires de confession spéciaux\n- Campagnes de charité pour les nécessiteux\n\nChaque paroisse organisera également des activités supplémentaires selon les besoins locaux. Nous encourageons tous les paroissiens à participer activement à ces exercices spirituels.\n\nUtilisons cette saison de Carême pour nous rapprocher de Dieu par la prière, le sacrifice et le service aux autres. Que ce soit un temps de renouveau spirituel et de préparation pour la joie de Pâques.'
        ],
        [
            'blog_post_id' => 4,
            'title' => 'Lancement de l\'Initiative de Santé Communautaire',
            'excerpt' => 'Le Diocèse de Byumba lance une nouvelle initiative de santé communautaire en partenariat avec les centres de santé locaux pour améliorer l\'accès aux soins de santé dans les zones rurales.',
            'content' => 'Le Diocèse de Byumba est fier d\'annoncer le lancement de notre Initiative de Santé Communautaire, un programme complet conçu pour améliorer l\'accès aux soins de santé et l\'éducation sanitaire dans les communautés rurales de notre diocèse.\n\nComposants du programme :\n- Cliniques mobiles visitant les zones reculées\n- Ateliers d\'éducation sanitaire\n- Programmes de santé maternelle et infantile\n- Éducation nutritionnelle\n- Campagnes de prévention des maladies\n- Sensibilisation à la santé mentale\n\nCette initiative est mise en œuvre en partenariat avec les centres de santé locaux, les agences gouvernementales et les organisations internationales de santé. Notre objectif est de s\'assurer que tous les membres de notre communauté aient accès à des services de santé de qualité.\n\nDes bénévoles sont nécessaires pour divers aspects du programme. Si vous avez une formation médicale ou voulez simplement aider votre communauté, veuillez contacter notre coordinateur des services sociaux.\n\nEnsemble, nous pouvons construire des communautés plus saines et démontrer l\'amour de Dieu en prenant soin des malades et des vulnérables.'
        ],
        [
            'blog_post_id' => 5,
            'title' => 'Inscription Ouverte au Programme de Formation de Foi pour Adultes',
            'excerpt' => 'Les inscriptions sont maintenant ouvertes pour le Programme de Formation de Foi pour Adultes 2024. Approfondissez votre compréhension de l\'enseignement catholique et développez votre relation avec Dieu.',
            'content' => 'Le Diocèse de Byumba invite tous les adultes à participer à notre Programme complet de Formation de Foi commençant le 1er février 2024. Ce programme est conçu pour les catholiques qui veulent approfondir leur compréhension de la foi et grandir dans leur parcours spirituel.\n\nCaractéristiques du programme :\n- Sessions d\'étude des Écritures\n- Cours de doctrine catholique\n- Éducation liturgique et sacramentelle\n- Ateliers de prière et de spiritualité\n- Enseignements de justice sociale\n- Discussions en petits groupes\n\nLes cours auront lieu tous les jeudis soirs de 19h00 à 20h30 au centre diocésain. Le programme dure 12 semaines et comprend du matériel à emporter pour étude supplémentaire.\n\nQue vous soyez un catholique de longue date ou quelqu\'un qui revient à la foi, ce programme offre quelque chose pour tout le monde. Nos catéchistes expérimentés vous guideront à travers des discussions engageantes et des applications pratiques de l\'enseignement catholique.\n\nFrais d\'inscription : 15 000 FRW (comprend tout le matériel)\nPour vous inscrire, contactez votre bureau paroissial ou appelez le Diocèse au +250 788 123 456.'
        ]
    ];
    
    // Insert French translations
    $insert_sql = "INSERT INTO blog_post_translations (blog_post_id, language_code, title, excerpt, content) VALUES (?, 'fr', ?, ?, ?)";
    $stmt = $db->prepare($insert_sql);
    
    foreach ($french_translations as $translation) {
        $stmt->execute([
            $translation['blog_post_id'],
            $translation['title'],
            $translation['excerpt'],
            $translation['content']
        ]);
    }
    
    echo "✓ Inserted French translations\n";

    // Kinyarwanda translations
    $kinyarwanda_translations = [
        [
            'blog_post_id' => 1,
            'title' => 'Gushyiraho Sisitemu Nshya yo Gusaba Ibyemezo ku Rubuga',
            'excerpt' => 'Diyosezi ya Byumba ishimiye kutangaza ko hashyizweho sisitemu nshya yo gusaba ibyemezo ku rubuga, bikoroshya abaturage ba paruwasi gusaba inyandiko z\'ingenzi.',
            'content' => 'Diyosezi ya Byumba ishimiye kutangaza ko hashyizweho sisitemu nshya yo gusaba ibyemezo ku rubuga. Iyi sisitemu ya digitale izoroshya inzira yo gusaba ibyemezo by\'ubwiyunge, iyemeza, ubukwe, n\'ibindi byemezo by\'ingenzi.\n\nIbiranga:\n- Kohereza ubusabe ku rubuga\n- Gushyira inyandiko ku rubuga\n- Gukurikirana uko imirimo igenda mu gihe nyacyo\n- Kwishyura mu buryo bwizewe\n- Gufasha mu ndimi nyinshi\n\nUbu abaturage ba paruwasi bashobora gusaba ibyemezo bava mu nzu zabo kandi bakakurikirana uko ubusabe bwabo bugenda. Iyi gahunda ni igice cy\'imbaraga zacu zo guhindura serivisi zacu no guha abaturage serivisi nziza.\n\nKugira ngo ubone sisitemu, sura urubuga rwacu ukore konti. Kugira ngo uhabwe ubufasha, hamagara ibiro byacu mu masaha y\'akazi.'
        ],
        [
            'blog_post_id' => 2,
            'title' => 'Kwihurira kw\'Urubyiruko rwa Diyosezi 2024',
            'excerpt' => 'Tuzane mu kwihurira kw\'urubyiruko rwa diyosezi kuva ku ya 15 kugeza ku ya 17 Gashyantare 2024, ku kiyaga cya Ruhondo. Wikendi y\'iterambere ry\'umwuka, ubusabane, n\'ibikorwa bishimishije by\'urubyiruko rw\'abagatolika.',
            'content' => 'Diyosezi ya Byumba itumira urubyiruko rwose rw\'abagatolika bafite imyaka 16-30 kwitabira kwihurira kwacu kw\'umwaka kuva ku ya 15 kugeza ku ya 17 Gashyantare 2024, ku kigo cy\'kwihurira cyiza cy\'ikiyaga cya Ruhondo.\n\nInsanganyamatsiko y\'uyu mwaka ni "Bahamagaye Gukorera" kandi izaba ifite:\n- Amasomo ashimishije y\'abashyitsi\n- Ibiganiro mu matsinda mato\n- Gusenga no gusengera\n- Ibikorwa byo kuruhuka\n- Ibitaramo by\'umuco\n- Amahirwe yo guhurirana\n\nKwihurira kugamije gushimangira kwizera, kubaka umuryango, no gushishikariza urubyiruko gufata uruhare runini mu paruwasi zabo no mu miryango yabo.\n\nAmafaranga yo kwiyandikisha: 25,000 FRW (harimo aho guturamo, ibiryo, n\'ibikoresho)\nItariki nyuma yo kwiyandikisha: 5 Gashyantare 2024\n\nKugira ngo wiyandikishe, vugana umushingamateka w\'urubyiruko wa paruwasi yawe cyangwa usure ibiro byacu. Imyanya ni mike - wiyandikishe vuba!'
        ],
        [
            'blog_post_id' => 3,
            'title' => 'Kwihugura kw\'Igihe cy\'Amatiku n\'Ibikorwa',
            'excerpt' => 'Mu gihe tugera ku gihe cyera cy\'amatiku, Diyosezi ya Byumba itangaza gahunda zidasanzwe n\'ibikorwa byo gufasha abizera kwihugura Pasika.',
            'content' => 'Igihe cy\'amatiku ni igihe cyo gusenga, kuraguza, no gutanga abakene mu gihe twihugura imitima yacu kugira ngo tuzihugure Pasika. Diyosezi ya Byumba yateguye gahunda zidasanzwe zo guherekeza abizera muri iki gihe cyera.\n\nIbikorwa by\'Amatiku:\n- Inzira z\'umusaraba buri cyumweru (ku wa gatanu saa kumi n\'ebyiri z\'umugoroba)\n- Kwihurira kw\'amatiku kw\'abantu bakuru (Werurwe 2-3)\n- Gahunda y\'amatiku y\'abana\n- Gahunda zidasanzwe zo kwicuza\n- Gukusanya impano z\'abakene\n\nBuri paruwasi nayo izategura ibikorwa byongeyeho ukurikije ibikenewe byaho. Dushishikariza abaturage bose ba paruwasi kwitabira byimazeyo muri aya mahugurwa y\'umwuka.\n\nReka dukoreshe iki gihe cy\'amatiku kugira ngo twegere Imana binyuze mu gusenga, guhakana, no gukorera abandi. Reka iki kibe igihe cyo kuvugurura umwuka no kwihugura umunezero wa Pasika.'
        ],
        [
            'blog_post_id' => 4,
            'title' => 'Gutangiza Gahunda y\'Ubuzima bw\'Umuryango',
            'excerpt' => 'Diyosezi ya Byumba itangiza gahunda nshya y\'ubuzima bw\'umuryango mu bufatanye n\'ibigo by\'ubuzima byo hafi kugira ngo hazamurwe ubushobozi bwo kubona ubuvuzi mu cyaro.',
            'content' => 'Diyosezi ya Byumba ishimiye kutangaza gutangiza Gahunda yacu y\'Ubuzima bw\'Umuryango, gahunda yuzuye yateguwe kugira ngo hazamurwe ubushobozi bwo kubona ubuvuzi n\'uburezi ku buzima mu miryango y\'icyaro muri diyosezi yacu.\n\nIbice by\'gahunda:\n- Amavuriro y\'igendanwa asura uduce twinshi\n- Amahugurwa ku buzima\n- Gahunda z\'ubuzima bw\'ababyeyi n\'abana\n- Uburezi ku menya neza\n- Ubukangurambaga bwo kurinda indwara\n- Kumenyekanisha ubuzima bw\'ubwoba\n\nIyi gahunda ikorwa mu bufatanye n\'ibigo by\'ubuzima byo hafi, inzego za leta, n\'imiryango mpuzamahanga y\'ubuzima. Intego yacu ni ukwemeza ko abanyamuryango bacu bose babona serivisi z\'ubuzima nziza.\n\nHakenewe abakorerabushake mu bice bitandukanye by\'gahunda. Niba ufite amahugurwa y\'ubuvuzi cyangwa ushaka gufasha umuryango wawe gusa, nyamuneka vugana umushingamateka w\'imirimo y\'imibereho myiza.\n\nTwese hamwe, dushobora kubaka imiryango ifite ubuzima bwiza kandi twerekane urukundo rw\'Imana binyuze mu kwita ku barwayi n\'abashaka ubufasha.'
        ],
        [
            'blog_post_id' => 5,
            'title' => 'Kwiyandikisha mu Gahunda yo Guhugura Kwizera kw\'Abantu Bakuru Byafunguye',
            'excerpt' => 'Kwiyandikisha byafunguye kuri Gahunda yo Guhugura Kwizera kw\'Abantu Bakuru 2024. Kwimbura ubumenyi bwawe bw\'inyigisho za gatolika kandi ukure mu isano yawe n\'Imana.',
            'content' => 'Diyosezi ya Byumba itumira abantu bakuru bose kwitabira Gahunda yacu yuzuye yo Guhugura Kwizera itangira ku ya 1 Gashyantare 2024. Iyi gahunda yateguwe abagatolika bashaka kwimbura ubumenyi bwabo bw\'kwizera no gukura mu rugendo rwabo rw\'umwuka.\n\nIbiranga gahunda:\n- Amasomo yo kwiga Inyandiko Ntagatifu\n- Amasomo y\'inyigisho za gatolika\n- Uburezi ku mihango n\'ibihangano\n- Amahugurwa yo gusenga n\'umwuka\n- Inyigisho z\'ubutabera bw\'imibereho\n- Ibiganiro mu matsinda mato\n\nAmasomo azakorwa buri wa kane nimugoroba kuva saa moya kugeza saa mbiri n\'igice ku kigo cya diyosezi. Gahunda imara ibyumweru 12 kandi ikaba ifite ibikoresho byo kwiga mu rugo.\n\nUba uri umunyagatolika w\'igihe kirekire cyangwa umuntu ugaruka ku kwizera, iyi gahunda ifite ikintu kuri buri wese. Abarimu bacu b\'ubunararibonye bazakuyobora mu biganiro bishimishije n\'ibikorwa bifatika by\'inyigisho za gatolika.\n\nAmafaranga yo kwiyandikisha: 15,000 FRW (harimo ibikoresho byose)\nKugira ngo wiyandikishe, vugana ibiro bya paruwasi yawe cyangwa hamagara Diyosezi kuri +250 788 123 456.'
        ]
    ];

    // Insert Kinyarwanda translations
    $insert_sql = "INSERT INTO blog_post_translations (blog_post_id, language_code, title, excerpt, content) VALUES (?, 'rw', ?, ?, ?)";
    $stmt = $db->prepare($insert_sql);

    foreach ($kinyarwanda_translations as $translation) {
        $stmt->execute([
            $translation['blog_post_id'],
            $translation['title'],
            $translation['excerpt'],
            $translation['content']
        ]);
    }

    echo "✓ Inserted Kinyarwanda translations\n";

    echo "French and Kinyarwanda translations added successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
