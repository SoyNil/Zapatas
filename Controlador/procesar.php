<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cantidadZapatas = (int)$_POST['cantidadZapatas'];

    // Inicializar arrays
    $baseArray = [];
    $alturaArray = [];
    $tipoAceroArray = [];

    // Recorrer y llenar arrays con los datos enviados por el formulario
    for ($i = 0; $i < $cantidadZapatas; $i++) {
        $baseArray[] = (float)$_POST['base'][$i]/(75);
        $alturaArray[] = (float)$_POST['altura'][$i]/75;
        $tipoAceroArray[] = (float)$_POST['tipoAcero'][$i];
    }
    echo "<pre>";
    print_r($baseArray);
    print_r($alturaArray);
    print_r($tipoAceroArray);
    echo "</pre>";
    $altura = max($alturaArray); // Obtener el valor máximo de altura
    $base = max($baseArray); // Obtener el valor máximo de altura
    $alturaTabla = $altura + 3.5; // Ahora $alturaTabla se basa en el valor máximo
    $baseTabla = $base + 3.4;
    $contenido_dxf = "";
    $contenido_dxf .= "0\nSECTION\n2\nENTITIES\n";
    // Definir el offset para dibujar los cuadrados
    $offsetX = 3; // Offset para el eje X

    // Generar cuadrados para cada columneta
    for ($i = 0; $i < $cantidadZapatas; $i++) {
        $contenido_dxf .= "0\nPOLYLINE\n8\n0\n66\n1\n70\n8\n62\n5\n";
        
        // Definir los vértices del cuadrado
        $vertices = array(
            array($offsetX, 0.5), // Esquina inferior izquierda
            array($offsetX + $baseArray[$i], 0.5), // Esquina inferior derecha
            array($offsetX + $baseArray[$i], $alturaArray[$i] + 0.5), // Esquina superior derecha
            array($offsetX, $alturaArray[$i] + 0.5), // Esquina superior izquierda
            array($offsetX, 0.5) // Cerrar el polígono
        );

        // Agregar los vértices al contenido DXF
        foreach ($vertices as $vertex) {
            $x = $vertex[0];
            $y = $vertex[1];
            $contenido_dxf .= "0\nVERTEX\n8\n0\n10\n$x\n20\n$y\n30\n0\n"; // Z=0 para 2D
        }
        $contenido_dxf .= "0\nSEQEND\n";
        //Obtener el tipo de acero
        $tipoAcero = $tipoAceroArray[$i];
        if ($tipoAcero == 0.0127 ) {
            $tipoAcero1 = "1/4";
        } elseif ($tipoAcero == 0.019039999999999998 ) {
            $tipoAcero1 = "3/8";
        } elseif ($tipoAcero == 0.0254 ) {
            $tipoAcero1 = "1/2";
        } elseif ($tipoAcero == 0.03174 ) {
            $tipoAcero1 = "5/8";
        } elseif ($tipoAcero == 0.0381 ) {
            $tipoAcero1 = "3/4";
        } elseif ($tipoAcero == 0.0508) {
            $tipoAcero1 = "1";
        }
        $yOffset= 0.05;
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n7\n10\n" . ($offsetX+0.05) . "\n20\n" . (0.55) . "\n11\n" . ($baseArray[$i] + $offsetX) . "\n21\n" . (0.55) . "\n";
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n7\n10\n" . (($baseArray[$i] + $offsetX)-0.05) . "\n20\n" . (0.5) . "\n11\n" . (($baseArray[$i] + $offsetX)-0.05) . "\n21\n" . (0.40+$alturaArray[$i] + $yOffset) . "\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n62\n7\n10\n" . (($baseArray[$i] + $offsetX)-0.05) . "\n20\n" . (0.60) . "\n40\n0.04\n1\n$tipoAcero1\"@0.20m\n50\n90\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n62\n7\n10\n" . ($offsetX+0.05) . "\n20\n" . (0.55) . "\n40\n0.04\n1\n$tipoAcero1\"@0.20m\n";

        $contenido_dxf .= "0\nLINE\n8\n0\n62\n7\n10\n" . $offsetX . "\n20\n" . (0.5+$alturaArray[$i] + $yOffset) . "\n11\n" . ($baseArray[$i] + $offsetX) . "\n21\n" . (0.5+$alturaArray[$i] + $yOffset) . "\n";
        $texBasex = ($baseArray[$i] * 75); 
        $contenido_dxf .= "0\nTEXT\n8\n0\n62\n7\n10\n" . (($baseArray[$i] / 2) + $offsetX) . "\n20\n" . ((0.5+$alturaArray[$i] + $yOffset + 0.07)) . "\n40\n0.05\n1\n$texBasex\n";
        $xOffset = ($baseArray[$i] + $offsetX) + 0.05;
        $contenido_dxf .= "0\nLINE\n8\n0\n62\n7\n10\n$xOffset\n20\n0.5\n11\n$xOffset\n21\n" . ($alturaArray[$i] + 0.5) . "\n";
        $texBasey = ($alturaArray[$i] * 75); 
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($xOffset + 0.02) . "\n20\n" . (($alturaArray[$i] / 2) + 0.5) . "\n40\n0.05\n1\n$texBasey\n";
        $var=$i+1;
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX-0.5) . "\n20\n" . ($alturaTabla - 0.15) . "\n40\n0.1\n1\nDETALLE\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX-0.7) . "\n20\n" . ($alturaTabla - 0.35) . "\n40\n0.1\n1\nZapata Aislada $var\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX-0.4) . "\n20\n" . ($alturaTabla - 0.55) . "\n40\n0.1\n1\n1.50 m\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX-0.4) . "\n20\n" . ($alturaTabla - 0.75) . "\n40\n0.1\n1\n0.60 m\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX-0.6) . "\n20\n" . ($alturaTabla - 0.95) . "\n40\n0.1\n1\n210 kg/cm2\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX-0.6) . "\n20\n" . ($alturaTabla - 1.15) . "\n40\n0.1\n1\n4200 kg/cm2\n";
        $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . ($offsetX) . "\n20\n" . ($alturaTabla - 1.35) . "\n40\n0.1\n1\n10 cm\n";

        // Actualizar el offset para el siguiente cuadrado (ajustar según el tamaño deseado)
        $offsetX += $baseArray[$i] + 2; // Aumentar el offset por el tamaño de la base y un espacio adicional
    }
    //Tabla    
    // Crear líneas al final de cada terminación de base
    for ($i = 0; $i < $cantidadZapatas; $i++) {
        if ($i == 0) {
            // Primera columneta
            $x1 = $baseTabla; // Posición actual de la base
            $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n0\n30\n0\n"; // Punto inicial de la línea
            $contenido_dxf .= "11\n$x1\n21\n" . ($alturaTabla) . "\n31\n0\n"; // Punto final de la línea
            $contenido_dxf .= "62\n1\n";
        } else {
            // Columnetas adicionales
            $baseTabla += $baseArray[$i] + 2; // Actualizar baseTabla
            $x1 = $baseTabla; // Posición actual de la base
            $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n0\n30\n0\n"; // Punto inicial de la línea
            $contenido_dxf .= "11\n$x1\n21\n" . ($alturaTabla) . "\n31\n0\n"; // Punto final de la línea
            $contenido_dxf .= "62\n1\n";
        }
    }
    $contenido_dxf .= "0\nPOLYLINE\n8\n0\n66\n1\n70\n8\n62\n1\n";
    $verticestabla = array(
        array(0, 0),
        array($baseTabla, 0),
        array($baseTabla, $alturaTabla),
        array(0, $alturaTabla),
        array(0, 0),
    );
    foreach ($verticestabla as $vertex) {
        $x = $vertex[0];
        $y = $vertex[1];
        $contenido_dxf .= "0\nVERTEX\n8\n0\n10\n$x\n20\n$y\n";
    }
    
    //TextoTablas
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n0.5\n20\n" . ($alturaTabla - 0.35) . "\n40\n0.1\n1\nSECCIÓN\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n0.8\n20\n" . ($alturaTabla - 0.55) . "\n40\n0.1\n1\nDf\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n0.8\n20\n" . ($alturaTabla - 0.75) . "\n40\n0.1\n1\nhz\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n0.8\n20\n" . ($alturaTabla - 0.95) . "\n40\n0.1\n1\nfc.\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n0.8\n20\n" . ($alturaTabla - 1.15) . "\n40\n0.1\n1\nfy\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n0.6\n20\n" . ($alturaTabla - 1.35) . "\n40\n0.1\n1\nsolado\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.06) . "\n20\n" . (0.2) . "\n40\n0.05\n1\nESCALA\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.10) . "\n20\n" . (0.1) . "\n40\n0.05\n1\n1/75\n";
    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (0.3) . "\n20\n" . ($altura) . "\n40\n0.2\n1\nCUADRO DE ZAPATAS\n50\n90\n";

    $contenido_dxf .= "0\nTEXT\n8\n0\n10\n" . (1) . "\n20\n" . (($altura+2.1)/3) . "\n40\n0.2\n1\nDETALLES\n50\n90\n";
    $contenido_dxf .= "0\nSEQEND\n";
    //Verticales
    $contenido_dxf .= "62\n1\n";
    $verticestablaH = array(
        array(0.4, 0),
        array(0.4, $alturaTabla),
    );
    foreach ($verticestablaH as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticestablaH[($i + 1) % count($verticestablaH)][0];
        $y2 = $verticestablaH[($i + 1) % count($verticestablaH)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n62\n1\n";
    }
    $verticestablaH = array(
        array(1.4, 0),
        array(1.4, $alturaTabla ),
    );
    foreach ($verticestablaH as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticestablaH[($i + 1) % count($verticestablaH)][0];
        $y2 = $verticestablaH[($i + 1) % count($verticestablaH)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n62\n1\n";
    }
    //Horizontales
    $verticesVE = array(
        array(0.4, $alturaTabla - 0.2),
        array($baseTabla, $alturaTabla - 0.2),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0.4, $alturaTabla - 0.4),
        array($baseTabla, $alturaTabla - 0.4),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n"; // Establecer el color rojo
    }
    $verticesVE = array(
        array(0.4, $alturaTabla - 0.6),
        array($baseTabla, $alturaTabla - 0.6),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0.4, $alturaTabla - 0.8),
        array($baseTabla, $alturaTabla - 0.8),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0.4, $alturaTabla - 1.0),
        array($baseTabla, $alturaTabla - 1.0),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0.4, $alturaTabla - 1.2),
        array($baseTabla, $alturaTabla - 1.2),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0.4, $alturaTabla - 1.4),
        array($baseTabla, $alturaTabla - 1.4),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $verticesVE = array(
        array(0, 0.3),
        array(0.4, 0.3),
    );
    foreach ($verticesVE as $i => $vertex) {
        $x1 = $vertex[0];
        $y1 = $vertex[1];
        $x2 = $verticesVE[($i + 1) % count($verticesVE)][0];
        $y2 = $verticesVE[($i + 1) % count($verticesVE)][1];
        $contenido_dxf .= "0\nLINE\n8\n0\n10\n$x1\n20\n$y1\n11\n$x2\n21\n$y2\n";
        $contenido_dxf .= "62\n1\n";
    }
    $contenido_dxf .= "0\nENDSEC\n0\nEOF";
    $archivoDXF = 'Columna-rectangular-cuadrado.dxf';
    $rutaArchivo = __DIR__ . '/' . $archivoDXF;
    file_put_contents($rutaArchivo, $contenido_dxf);
    $rutaAutoCAD = 'C:\Program Files\Autodesk\AutoCAD 2021\acad.exe';
    $comando = 'start "" "' . $rutaAutoCAD . '" "' . $rutaArchivo . '"';
    // Ejecutar el comando
    shell_exec($comando);
    echo "AutoCAD iniciado con el archivo DXF basado en las dimensiones proporcionadas.";
}
?>
