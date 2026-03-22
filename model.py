import sys
import json
import random
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score

try:
    # =====================================
    # Validate Argument Count
    # =====================================
    if len(sys.argv) != 10:
        raise Exception("Invalid number of input arguments")

    # =====================================
    # Generate Realistic Synthetic Dataset
    # =====================================
    X = []
    y = []

    for i in range(400):

        income = random.randint(1000, 10000)
        loan = random.randint(50, 600)
        credit = random.randint(0, 1)
        dependents = random.randint(0, 5)
        education = random.randint(0, 1)      # 0=Not Graduate,1=Graduate
        employment = random.randint(0, 1)     # 0=Self-Employed,1=Salaried
        duration = random.randint(6, 60)
        cibil = random.randint(300, 900)
        assets = random.randint(0, 500000)

        # ===============================
        # Risk Score Calculation
        # ===============================
        risk_score = 0

        # CIBIL Score (Major Factor)
        if cibil > 750:
            risk_score += 30
        elif cibil > 650:
            risk_score += 15
        else:
            risk_score -= 25

        # Income Stability
        if income > 7000:
            risk_score += 20
        elif income > 4000:
            risk_score += 10
        else:
            risk_score -= 15

        # Debt to Income Ratio
        dti = loan / (income + 1)
        if dti < 0.3:
            risk_score += 15
        elif dti < 0.6:
            risk_score += 5
        else:
            risk_score -= 20

        # Employment Type (Aligned with your form)
        if employment == 1:   # Salaried
            risk_score += 10
        else:                 # Self-Employed
            risk_score += 5

        # Education Impact
        if education == 1:    # Graduate
            risk_score += 5

        # Assets Backup
        if assets > 200000:
            risk_score += 10

        # Dependents Impact
        if dependents > 3:
            risk_score -= 10

        # Credit History
        if credit == 1:
            risk_score += 15
        else:
            risk_score -= 20

        # Final Label Decision
        if risk_score >= 35:
            label = 1
        else:
            label = 0

        X.append([
            income, loan, credit, dependents,
            education, employment, duration,
            cibil, assets
        ])
        y.append(label)

    # =====================================
    # Train/Test Split
    # =====================================
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.3, random_state=42
    )

    # =====================================
    # Train Model
    # =====================================
    model = RandomForestClassifier(
        n_estimators=200,
        max_depth=10,
        random_state=42
    )

    model.fit(X_train, y_train)

    y_pred = model.predict(X_test)
    accuracy = round(accuracy_score(y_test, y_pred) * 100, 2)

    # =====================================
    # Get Inputs From PHP
    # =====================================
    income = int(float(sys.argv[1]))
    loan = int(float(sys.argv[2]))
    credit = int(float(sys.argv[3]))
    dependents = int(float(sys.argv[4]))
    education = int(float(sys.argv[5]))
    employment = int(float(sys.argv[6]))
    duration = int(float(sys.argv[7]))
    cibil = int(float(sys.argv[8]))
    assets = int(float(sys.argv[9]))

    # =====================================
    # Prediction
    # =====================================
    input_data = [[
        income, loan, credit, dependents,
        education, employment, duration,
        cibil, assets
    ]]

    prediction = model.predict(input_data)
    prob = model.predict_proba(input_data)

    confidence = round(max(prob[0]) * 100, 2)

    # ===============================
    # Risk Level Classification
    # ===============================
    if prediction[0] == 1:
        if confidence > 85:
            status = "Loan Approved - Low Risk"
        else:
            status = "Loan Approved - Medium Risk"
    else:
        status = "Loan Rejected - High Risk"

    result = {
        "status": status,
        "confidence": confidence,
        "accuracy": accuracy
    }

    print(json.dumps(result))

except Exception as e:
    error_result = {
        "status": "Prediction Error",
        "confidence": 0,
        "accuracy": 0,
        "error": str(e)
    }
    print(json.dumps(error_result))