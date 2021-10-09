import clipboard
# print('insert vars comma split')

variables = """
id,
uid,
php_sess_id,
src,
revoked,
revoke_date,
login_time,
expire_time
"""

varArr = variables.replace(' ', '').replace('\n', '').strip().split(',')

toClip = ['', '', '', '', '']

for var in varArr:
    toClip[0] += var + ',\n'
    toClip[1] += (var + ' = :' + var + ',\n')
    toClip[2] += ("'" + var + "'" + ' => $model->get' + var + ",\n")
    toClip[3] += (":" + var + ',\n')
    toClip[4] += ("$this->" + var + ' = $row[\'' + var + "\'];\n")

# print(toClip[0] + "\n" + toClip[3] + '\n' + toClip[1] + '\n' + toClip[2])

# print(varArr)
# print('\n\n')
# print(toClip)
print('\n\n'.join(toClip))

data = '\n\n'.join(toClip)
clipboard.copy(data)
# copy2clip(data)