#!/bin/bash
BASE="http://localhost:8000/api/books"

echo "== Test 1: GET semua buku =="
curl -s $BASE | jq -c '{success, total: (.data | length)}'

echo "== Test 2: POST buku baru =="
ID=$(curl -s -X POST $BASE \
  -H "Content-Type: application/json" \
  -d '{"judul":"Buku Tes","penulis":"Penulis Tes","tahun":2024,"stok":3}' | jq -r '.data.id')
echo "ID baru: $ID"

echo "== Test 3: GET buku ID $ID =="
curl -s $BASE/$ID | jq -c .

echo "== Test 4: PUT ubah buku =="
curl -s -X PUT $BASE/$ID \
  -H "Content-Type: application/json" \
  -d '{"judul":"Buku Tes Revisi","penulis":"Penulis Tes","tahun":2025,"stok":9}' | jq -c .

echo "== Test 5: DELETE buku =="
curl -s -X DELETE $BASE/$ID | jq -c .

echo "== Test 6: Validasi error =="
curl -s -X POST $BASE \
  -H "Content-Type: application/json" \
  -d '{"judul":""}' | jq -c .