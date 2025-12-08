<?php 
namespace App\Services;

use App\Models\Produit;
use App\Traits\UploadTrait;

class ProduitService
{
    use UploadTrait;

    public function createProduit(array $data, $vendeur_id)
    {
        $produit = Produit::create([
            'vendeur_id' => $vendeur_id,
            'categorie_id' => $data['categorie_id'],
            'nom' => $data['nom'],
            'description' => $data['description'] ?? null,
            'prix' => $data['prix'],
            'stock_qtte' => $data['stock_qtte']
        ]);

        // Upload images
        if (!empty($data['images'])) {
            foreach ($data['images'] as $image) {
                $produit->medias()->create([
                    'image_link' => $this->uploadFile($image, 'produits/images')
                ]);
            }
        }

        // Upload documents
        if (!empty($data['documents'])) {
            foreach ($data['documents'] as $doc) {
                $produit->documents()->create([
                    'document_link' => $this->uploadFile($doc, 'produits/documents')
                ]);
            }
        }

        return $produit;
    }

    public function updateProduit(Produit $produit, array $data)
    {
        $produit->update($data);
        return $produit;
    }

    public function deleteProduit(Produit $produit)
    {
        $produit->delete();
        return true;
    }
}