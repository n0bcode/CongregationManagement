if [ -f "managing-congregation/artisan" ]; then
  echo "PASS: Laravel project initialized"
  if curl -s --head  --request GET http://localhost:8000 | grep "200 OK" > /dev/null; then 
     echo "PASS: App is running on port 8000"
     exit 0
  else
     echo "FAIL: App is not responding on port 8000"
     exit 1
  fi
else
  echo "FAIL: Laravel project not found"
  exit 1
fi
