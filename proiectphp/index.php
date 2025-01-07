
<?php 
    include 'app/views/partials/header.php'; 
    require_once 'app/controllers/CarteController.php';
    require_once 'app/controllers/ImprumutController.php';

    $carteController = new CarteController();
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca Online</title>
    <link rel="stylesheet" href="/proiectphp/public/style.css"> 
</head>
<body>
    
    <main>
        <section>
            <h2>Despre Biblioteca Noastră</h2>
            <p>Biblioteca Online vă oferă acces la o colecție vastă de cărți din diferite domenii. Utilizatorii pot împrumuta cărți și pot verifica disponibilitatea acestora în timp real. Biblioteca noastră este deschisă pentru toți iubitorii de lectură!</p>
        </section>
        
        <section>
            <h2>Funcționalități</h2>
            <ul>
                <li>Catalog de cărți organizat după autor, gen și disponibilitate</li>
                <li>Împrumuturi și returnări de cărți</li>
                <li>Autentificare pentru a gestiona contul personal</li>
                <li>Înregistrare pentru noi utilizatori</li>
            </ul>
        </section>
        <h2>Cărțile Noastre</h2>
        <canvas id="graficGenuri"></canvas>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const dateGenuri = <?php echo json_encode($carteController->getCartiDupaGen()); ?>;
            const etichete = dateGenuri.map(item => item.gen);
            const valori = dateGenuri.map(item => item.numar);

            const ctx = document.getElementById('graficGenuri').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: etichete,
                    datasets: [{
                        label: 'Distribuția cartilor pe genuri',
                        data: valori,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(75, 192, 93, 0.2)',
                            'rgba(192, 75, 159, 0.2)',
                            'rgba(192, 147, 75, 0.2)'
                        ],
                        borderColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 206, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(75, 192, 87)',
                            'rgb(192, 75, 165)',
                            'rgb(192, 134, 75)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        </script>
    </main>
    
    <footer>
    </footer>
</body>
</html>