from fastapi import FastAPI
from pydantic import BaseModel
from sentence_transformers import SentenceTransformer
import uvicorn

# Charger le modèle (ex: all-MiniLM, rapide et efficace)
model = SentenceTransformer("all-MiniLM-L6-v2")

app = FastAPI()

class TextRequest(BaseModel):
    text: str

@app.post("/embed")
def get_embedding(req: TextRequest):
    vector = model.encode(req.text).tolist()
    return {"embedding": vector}

if __name__ == "__main__":
    uvicorn.run(app, host="0.0.0.0", port=8001)
