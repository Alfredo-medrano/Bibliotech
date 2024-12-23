<?php

// Abstracción
class Autor {
    private int $id; // Encapsulamiento: Atributos privados
    private string $nombre;

    public function __construct(int $id, string $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getNombre(): string {
        return $this->nombre; //Acceso a datos a través de métodos públicos
    }

    public function __toString(): string { 
        return $this->nombre;
    }
}

class Categoria {
    private int $id; // Encapsulamiento.
    private string $nombre;

    public function __construct(int $id, string $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function getNombre(): string {
        return $this->nombre; // Encapsulamiento.
    }

    public function __toString(): string { 
        return $this->nombre;
    }
}

class Libro {
    private int $id;
    private string $titulo;
    private Autor $autor;
    private Categoria $categoria;
    private string $estado;

    public function __construct(int $id, string $titulo, Autor $autor, Categoria $categoria) {
        $this->id = $id;
        $this->titulo = $titulo;
        $this->autor = $autor;
        $this->categoria = $categoria;
        $this->estado = 'Disponible';
    }

    public function getTitulo(): string {
        return $this->titulo;
    }

    public function getAutor(): Autor {
        return $this->autor;
    }

    public function getCategoria(): Categoria {
        return $this->categoria;
    }

    public function prestar(): bool { // Abstracción de la acción de préstamo.
        if ($this->estado == 'Disponible') {
            $this->estado = 'Prestado';
            return true;
        }
        return false;
    }

    public function devolver(): void { // Abstracción de la acción de devolución.
        $this->estado = 'Disponible';
    }

    public function __toString(): string { // Polimorfismo.
        return "Título: " . $this->titulo . ", Autor: " . $this->autor . ", Categoría: " . $this->categoria . ", Estado: " . $this->estado;
    }
}

// Herencia y Polimorfismo,funcionalidad a través de clases hijas.
class Usuario {
    private int $id; 
    private string $nombre;
    private array $prestamos = [];

    public function __construct(int $id, string $nombre) {
        $this->id = $id;
        $this->nombre = $nombre;
    }

    public function pedirPrestamo(string $titulo, Biblioteca $biblioteca): void {
        try {
            if ($biblioteca->prestarLibro($titulo)) {
                $this->prestamos[] = $titulo;
                echo "[$this->nombre] Préstamo realizado con éxito: " . $titulo . PHP_EOL;
            }
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    public function devolverPrestamo(string $titulo, Biblioteca $biblioteca): void {
        if (($key = array_search($titulo, $this->prestamos)) !== false) {
            unset($this->prestamos[$key]);
            try {
                $biblioteca->devolverLibro($titulo);
                echo "[$this->nombre] Devolución exitosa: " . $titulo . PHP_EOL;
            } catch (Exception $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        } else {
            echo "[$this->nombre] Este libro no está registrado como prestado." . PHP_EOL;
        }
    }
}

// Abstracción, clase que agrupa objetos relacionados
class Biblioteca {
    private array $libros = []; // Encapsulamiento.

    public function agregarLibro(Libro $libro): void {
        $this->libros[$libro->getTitulo()] = $libro;
    }

    public function prestarLibro(string $titulo): bool {
        if (!isset($this->libros[$titulo])) {
            throw new Exception("El libro '$titulo' no existe en la biblioteca.");
        }

        if (!$this->libros[$titulo]->prestar()) {
            throw new Exception("El libro '$titulo' ya está prestado.");
        }

        return true;
    }

    public function devolverLibro(string $titulo): void {
        if (!isset($this->libros[$titulo])) {
            throw new Exception("El libro '$titulo' no existe en la biblioteca.");
        }

        $this->libros[$titulo]->devolver();
    }

    public function mostrarLibros(): void { // Polimorfismo, usamos un metodo generico para mostrar libros.
        foreach ($this->libros as $libro) {
            echo $libro . PHP_EOL;
        }
    }
}

// Instancias de las clases
$autor1 = new Autor(1, 'Gabriel García Márquez');
$categoria1 = new Categoria(1, 'Ficción');
$libro1 = new Libro(1, 'Cien años de soledad', $autor1, $categoria1);
$libro2 = new Libro(2, 'El amor en los tiempos del cólera', $autor1, $categoria1);
$biblioteca = new Biblioteca();
$biblioteca->agregarLibro($libro1);
$biblioteca->agregarLibro($libro2);

$usuario1 = new Usuario(1, 'Juan Pérez');

$usuario1->pedirPrestamo('Cien años de soledad', $biblioteca);

echo PHP_EOL . "Libros disponibles en la biblioteca:" . PHP_EOL;
$biblioteca->mostrarLibros();

$usuario1->devolverPrestamo('Cien años de soledad', $biblioteca);

echo PHP_EOL . "Libros disponibles después de la devolución:" . PHP_EOL;
$biblioteca->mostrarLibros();

?>
