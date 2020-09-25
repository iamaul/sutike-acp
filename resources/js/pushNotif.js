async function postData(title='', body='') {
  // Default options are marked with *
  const response = await fetch('https://exp.host/--/api/v2/push/send', {
    method: 'POST', // *GET, POST, PUT, DELETE, etc.
    mode: 'no-cors', // no-cors, *cors, same-origin
    cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
    credentials: 'same-origin', // include, *same-origin, omit
    headers: {
                            'host': 'exp.host',
                            'accept': 'application/json',
                            'accept-encoding': 'gzip, deflate',
                            'content-type': 'application/json'
    },
    redirect: 'follow', // manual, *follow, error
    referrer: 'no-referrer', // no-referrer, *client
    body: JSON.stringify({
                                "to": fieldToken,
                                "sound":"default",     
                                "title": title,
                                "body": body
                            }) // body data type must match "Content-Type" header
  });
//   return await response.json(); // parses JSON response into native JavaScript objects
}

function appendLeadingZeroes(n){
  if(n <= 9){
    return "0" + n;
  }
  return n
}