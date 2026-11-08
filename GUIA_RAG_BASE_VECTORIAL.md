# Guía de Implementación RAG y Bases de Datos Vectoriales en Laravel

Este documento detalla la viabilidad, arquitecturas y estrategias para implementar un sistema **RAG (Retrieval-Augmented Generation)** en el proyecto actual utilizando **MySQL**, y cuándo considerar **PostgreSQL (pgvector)** o **MariaDB 11.4+**.

---

## 1. Conclusión y Viabilidad en el Proyecto Actual

> [!TIP]
> **Sí, es 100% viable implementar RAG en el proyecto actual con MySQL sin cambiar de base de datos.**

Para casos de uso orientados a documentos institucionales o sindicales (Contrato Colectivo de Trabajo, normativas internas, estatutos, actas, preguntas frecuentes):
* El volumen estimado de fragmentos (*chunks*) suele rondar entre **500 y 5,000 fragmentos**.
* Con ese volumen, **MySQL tradicional maneja perfectamente el almacenamiento** y PHP 8.3 calcula la similitud semántica en **menos de 20 milisegundos**.

---

## 2. Comparativa de Enfoques

| Característica | Opción 1: MySQL Actual (JSON / BLOB) | Opción 2: MariaDB 11.4+ (Vector Search) | Opción 3: PostgreSQL + `pgvector` | Opción 4: Vector DB Externa (Qdrant / Chroma) |
| :--- | :--- | :--- | :--- | :--- |
| **Cambios en infraestructura** | **Ninguno** (Funciona en Laragon y Hosting actual) | Actualizar el motor MariaDB a 11.4+ | Instalar PostgreSQL y extensión `pgvector` | Levantar servicio Docker o usar Cloud API |
| **Índice Vectorial Nativo** | No (Fuerza bruta / búsqueda secuencial) | Sí (`HNSW`, `FLAT`) | Sí (`HNSW`, `IVFFlat`) | Sí (`HNSW`) |
| **Capacidad recomendada** | Hasta ~10,000 fragmentos | Cientos de miles de fragmentos | Millones de fragmentos | Millones de fragmentos |
| **Tiempo de respuesta (<5,000 docs)** | ~5 - 20 ms | < 5 ms | < 5 ms | ~20 - 50 ms (latencia de red) |
| **Complejidad de despliegue** | **Mínima (Inmediata)** | Media | Media | Baja a Media |

---

## 3. Estrategia Recomendada: Comenzar con MySQL Actual

### A. Estructura de la Tabla (Migración Laravel)
Los embeddings devueltos por Gemini (modelo `gemini-embedding-001`) tienen 3,072 dimensiones. Se pueden almacenar como un array JSON:

```php
Schema::create('document_chunks', function (Blueprint $table) {
    $table->id();
    $table->string('document_name');      // Ej: "Contrato Colectivo de Trabajo 2024-2026"
    $table->string('section')->nullable(); // Ej: "Capítulo IV - Licencias y Permisos"
    $table->text('content');               // Texto del fragmento (ej. 300-800 palabras)
    $table->json('embedding');             // Vector de 3072 dimensiones [0.012, -0.045, ...]
    $table->json('metadata')->nullable();  // Metadatos adicionales (artículo, página, etc.)
    $table->timestamps();
});
```

### B. Cálculo de Similitud de Coseno en PHP
Al realizar una pregunta:
1. Se genera el embedding de la pregunta del usuario con Gemini.
2. Se consultan los embeddings almacenados en la base de datos.
3. Se calcula la **similitud de coseno**:

```php
/**
 * Calcula la similitud de coseno entre dos vectores numéricos.
 * Retorna un valor entre -1 y 1 (donde 1 es máxima similitud semántica).
 */
function cosineSimilarity(array $vecA, array $vecB): float
{
    $dotProduct = 0.0;
    $normA = 0.0;
    $normB = 0.0;
    $count = count($vecA);

    for ($i = 0; $i < $count; $i++) {
        $dotProduct += $vecA[$i] * $vecB[$i];
        $normA += $vecA[$i] * $vecA[$i];
        $normB += $vecB[$i] * $vecB[$i];
    }

    if ($normA == 0.0 || $normB == 0.0) {
        return 0.0;
    }

    return $dotProduct / (sqrt($normA) * sqrt($normB));
}
```

4. Se toman los mejores $K$ fragmentos (los de mayor similitud, típicamente los top 3 o 5).
5. Se envían esos fragmentos como contexto en el prompt a Gemini para generar la respuesta final fundamentada.

---

## 4. ¿Cuándo migrar a PostgreSQL o MariaDB 11.4+?

Conviene dar el salto cuando:
1. **Volumen de fragmentos:** Superas los 20,000 – 50,000 fragmentos y la búsqueda secuencial empiece a tomar más de 100 ms.
2. **Índices HNSW necesarios:** Requieres búsquedas por aproximación (*Approximate Nearest Neighbor*) a escala de millones de registros.

### Conexión híbrida en Laravel (Sin migrar el sistema de asistencia):
Si en el futuro requieres PostgreSQL con `pgvector`, **no necesitas migrar todo tu proyecto**. Laravel soporta múltiples conexiones de base de datos en `config/database.php`:

```php
// Tu sistema actual (asistencias, usuarios, eventos) sigue en:
'mysql' => [ ... ],

// Tu base de datos para vectores se agrega como:
'pgsql_vector' => [
    'driver' => 'pgsql',
    'host' => env('PGSQL_HOST', '127.0.0.1'),
    'database' => env('PGSQL_DATABASE', 'vector_db'),
    // ...
],
```

Y en tu modelo de Laravel solo especificas:
```php
class DocumentChunk extends Model
{
    protected $connection = 'pgsql_vector';
}
```

---

## 5. Resumen de Próximos Pasos Sugeridos

1. **Fase 1 (Actual):** Usar MySQL con columna `json` para probar el pipeline de RAG (chunking del CCT + generación de embeddings con `gemini-embedding-001`).
2. **Fase 2:** Implementar el endpoint o comando de consulta que tome la pregunta del usuario, busque los chunks más afines y le pida a Gemini la respuesta con base en ellos.
3. **Fase 3 (Solo si el volumen lo exige):** Evaluar si conviene activar una segunda conexión a PostgreSQL (`pgvector`) o actualizar MariaDB a 11.4+.
